<?php

namespace App\Http\Controllers;

use App\Repositories\ApiIntegrationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MarketplaceAuthController extends Controller
{
    public function __construct(
        private ApiIntegrationRepository $tokenRepository
    ) {}

    /**
     * Redirect to marketplace OAuth authorization
     */
    public function redirect(Request $request)
    {
        $tenantId = $this->getCurrentTenantId();

        if (!$tenantId) {
            return redirect()->route('dashboard')
                ->with('error', 'Geen actief bedrijf geselecteerd voor OAuth autorisatie.');
        }

        // Store state for security
        $state = base64_encode(json_encode([
            'tenant_id' => $tenantId,
            'csrf' => csrf_token(),
            'timestamp' => now()->timestamp,
        ]));

        session(['oauth_state' => $state]);

        $params = http_build_query([
            'client_id' => config('services.marketplace.client_id'),
            'redirect_uri' => config('services.marketplace.redirect_uri'),
            'scope' => config('services.marketplace.scope'),
            'response_type' => 'code',
            'state' => $state,
        ]);

        $authUrl = config('services.marketplace.base_url') . 
                  config('services.marketplace.auth_url', '/oauth/authorize') . 
                  '?' . $params;

        Log::info('Marketplace OAuth redirect', [
            'tenant_id' => $tenantId,
            'redirect_url' => $authUrl,
        ]);

        return redirect($authUrl);
    }

    /**
     * Handle OAuth callback from marketplace
     */
    public function callback(Request $request)
    {
        $code = $request->get('code');
        $state = $request->get('state');
        $error = $request->get('error');

        // Handle OAuth errors
        if ($error) {
            Log::error('Marketplace OAuth error', [
                'error' => $error,
                'error_description' => $request->get('error_description'),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'OAuth autorisatie mislukt: ' . $error);
        }

        // Validate state parameter
        if (!$state || $state !== session('oauth_state')) {
            Log::error('Invalid OAuth state', [
                'provided_state' => $state,
                'session_state' => session('oauth_state'),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Ongeldig OAuth state parameter. Probeer opnieuw.');
        }

        // Parse state to get tenant_id
        $stateData = json_decode(base64_decode($state), true);
        $tenantId = $stateData['tenant_id'] ?? null;

        if (!$tenantId) {
            return redirect()->route('dashboard')
                ->with('error', 'Geen tenant ID gevonden in OAuth state.');
        }

        // Exchange code for tokens
        try {
            $tokenResponse = $this->exchangeCodeForTokens($code);

            // Store tokens
            $expiresAt = isset($tokenResponse['expires_in']) 
                ? now()->addSeconds($tokenResponse['expires_in'])
                : now()->addHour();

            $this->tokenRepository->storeTokens(
                $tenantId,
                'marketplace',
                $tokenResponse['access_token'],
                $tokenResponse['refresh_token'] ?? null,
                $expiresAt,
                [
                    'scope' => $tokenResponse['scope'] ?? config('services.marketplace.scope'),
                    'token_type' => $tokenResponse['token_type'] ?? 'Bearer',
                ]
            );

            // Clear session state
            session()->forget('oauth_state');

            Log::info('Marketplace OAuth success', [
                'tenant_id' => $tenantId,
                'expires_at' => $expiresAt,
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Marketplace koppeling succesvol ingesteld!');

        } catch (\Exception $e) {
            Log::error('Marketplace token exchange failed', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenantId,
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Token uitwisseling mislukt: ' . $e->getMessage());
        }
    }

    /**
     * Exchange authorization code for access tokens
     */
    private function exchangeCodeForTokens(string $code): array
    {
        $response = Http::asForm()->post(
            config('services.marketplace.base_url') . config('services.marketplace.token_url', '/oauth/token'),
            [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => config('services.marketplace.client_id'),
                'client_secret' => config('services.marketplace.client_secret'),
                'redirect_uri' => config('services.marketplace.redirect_uri'),
            ]
        );

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Token exchange failed: ' . $response->body()
            );
        }

        return $response->json();
    }

    /**
     * Refresh token if needed (called by middleware or services)
     */
    public function refreshIfNeeded(?int $tenantId = null): bool
    {
        $tenantId = $tenantId ?? $this->getCurrentTenantId();

        if (!$tenantId) {
            return false;
        }

        $integration = $this->tokenRepository->getByTenantAndProvider($tenantId, 'marketplace');

        if (!$integration || !$integration->canRefresh()) {
            return false;
        }

        // Only refresh if token is expired or expires soon
        if (!$integration->isExpired() && !$integration->expiringSoon()) {
            return true;
        }

        try {
            $response = Http::asForm()->post(
                config('services.marketplace.base_url') . config('services.marketplace.token_url', '/oauth/token'),
                [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $integration->refresh_token,
                    'client_id' => config('services.marketplace.client_id'),
                    'client_secret' => config('services.marketplace.client_secret'),
                ]
            );

            if (!$response->successful()) {
                Log::error('Token refresh failed', [
                    'tenant_id' => $tenantId,
                    'response' => $response->body(),
                ]);
                return false;
            }

            $data = $response->json();
            $expiresAt = isset($data['expires_in']) 
                ? now()->addSeconds($data['expires_in'])
                : now()->addHour();

            $integration->updateTokens(
                $data['access_token'],
                $data['refresh_token'] ?? $integration->refresh_token,
                $expiresAt
            );

            Log::info('Token refreshed successfully', [
                'tenant_id' => $tenantId,
                'expires_at' => $expiresAt,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Token refresh exception', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get current tenant ID from session/context
     */
    private function getCurrentTenantId(): ?int
    {
        // Use your existing tenant resolution logic
        return tenant_id();
    }
}
