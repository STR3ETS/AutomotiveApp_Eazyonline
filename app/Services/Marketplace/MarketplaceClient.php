<?php

namespace App\Services\Marketplace;

use App\DTO\ListingDTO;
use App\DTO\ResponseDTO;
use App\Repositories\ApiIntegrationRepository;
use App\Exceptions\MarketplaceAuthException;
use App\Exceptions\MarketplaceRateLimitException;
use App\Exceptions\MarketplaceValidationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MarketplaceClient
{
    private PendingRequest $http;

    public function __construct(
        private ApiIntegrationRepository $tokenRepository,
        private string $baseUrl,
        private ?int $tenantId = null
    ) {
        $this->http = Http::timeout(30)
            ->retry(3, 1000)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'AutomotiveApp/1.0',
            ]);
    }

    /**
     * Create a new listing
     */
    public function createListing(ListingDTO $dto): ResponseDTO
    {
        $this->logOperation('create_listing', ['title' => $dto->title]);

        if (config('services.marketplace.mock', true)) {
            return $this->mockCreateListing($dto);
        }

        // Validate DTO
        if (!$dto->isValid()) {
            throw new MarketplaceValidationException(
                'Invalid listing data',
                $dto->validate()
            );
        }

        try {
            $response = $this->authenticatedRequest()
                ->post('/v1/listings', $dto->toArray());

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            $this->logError('create_listing_failed', $e);
            throw $e;
        }
    }

    /**
     * Update existing listing
     */
    public function updateListing(string $externalId, ListingDTO $dto): ResponseDTO
    {
        $this->logOperation('update_listing', [
            'external_id' => $externalId,
            'title' => $dto->title
        ]);

        if (config('services.marketplace.mock', true)) {
            return $this->mockUpdateListing($externalId, $dto);
        }

        try {
            $response = $this->authenticatedRequest()
                ->put("/v1/listings/{$externalId}", $dto->toArray());

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            $this->logError('update_listing_failed', $e, ['external_id' => $externalId]);
            throw $e;
        }
    }

    /**
     * Upload image to listing
     */
    public function uploadImage(string $externalId, UploadedFile|string $file): ResponseDTO
    {
        $this->logOperation('upload_image', ['external_id' => $externalId]);

        if (config('services.marketplace.mock', true)) {
            return $this->mockUploadImage($externalId, $file);
        }

        try {
            $request = $this->authenticatedRequest();

            if ($file instanceof UploadedFile) {
                $response = $request->attach('image', $file->get(), $file->getClientOriginalName())
                    ->post("/v1/listings/{$externalId}/images");
            } else {
                // Handle file path/stream
                $response = $request->attach('image', file_get_contents($file), basename($file))
                    ->post("/v1/listings/{$externalId}/images");
            }

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            $this->logError('upload_image_failed', $e, ['external_id' => $externalId]);
            throw $e;
        }
    }

    /**
     * Publish listing
     */
    public function publish(string $externalId): ResponseDTO
    {
        $this->logOperation('publish_listing', ['external_id' => $externalId]);

        if (config('services.marketplace.mock', true)) {
            return $this->mockPublish($externalId);
        }

        try {
            $response = $this->authenticatedRequest()
                ->post("/v1/listings/{$externalId}/publish");

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            $this->logError('publish_listing_failed', $e, ['external_id' => $externalId]);
            throw $e;
        }
    }

    /**
     * Unpublish listing
     */
    public function unpublish(string $externalId): ResponseDTO
    {
        $this->logOperation('unpublish_listing', ['external_id' => $externalId]);

        if (config('services.marketplace.mock', true)) {
            return $this->mockUnpublish($externalId);
        }

        try {
            $response = $this->authenticatedRequest()
                ->post("/v1/listings/{$externalId}/unpublish");

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            $this->logError('unpublish_listing_failed', $e, ['external_id' => $externalId]);
            throw $e;
        }
    }

    /**
     * Get authenticated HTTP client
     */
    private function authenticatedRequest(): PendingRequest
    {
        $token = $this->getValidAccessToken();

        if (!$token) {
            throw new MarketplaceAuthException('No valid access token available');
        }

        return $this->http->withToken($token);
    }

    /**
     * Get valid access token (refresh if needed)
     */
    private function getValidAccessToken(): ?string
    {
        $token = $this->tokenRepository->getValidToken($this->tenantId, 'marketplace');

        if ($token) {
            return $token;
        }

        // Try to refresh token
        $refreshToken = $this->tokenRepository->getRefreshToken($this->tenantId, 'marketplace');

        if ($refreshToken) {
            $this->refreshAccessToken($refreshToken);
            return $this->tokenRepository->getValidToken($this->tenantId, 'marketplace');
        }

        return null;
    }

    /**
     * Refresh access token
     */
    private function refreshAccessToken(string $refreshToken): void
    {
        $this->logOperation('refresh_token', []);

        try {
            $response = Http::asForm()->post($this->baseUrl . '/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => config('services.marketplace.client_id'),
                'client_secret' => config('services.marketplace.client_secret'),
            ]);

            if (!$response->successful()) {
                throw new MarketplaceAuthException('Failed to refresh token: ' . $response->body());
            }

            $data = $response->json();
            $expiresAt = isset($data['expires_in']) 
                ? now()->addSeconds($data['expires_in'])
                : now()->addHour();

            $this->tokenRepository->updateToken(
                $this->tenantId,
                'marketplace',
                $data['access_token'],
                $data['refresh_token'] ?? $refreshToken,
                $expiresAt
            );

            $this->logOperation('refresh_token_success', []);
        } catch (\Exception $e) {
            $this->logError('refresh_token_failed', $e);
            throw new MarketplaceAuthException('Token refresh failed', 0, $e);
        }
    }

    /**
     * Handle API response
     */
    private function handleResponse(\Illuminate\Http\Client\Response $response): ResponseDTO
    {
        $responseDto = ResponseDTO::fromHttpResponse($response);

        if ($response->status() === 401) {
            throw new MarketplaceAuthException('Authentication failed');
        }

        if ($response->status() === 429) {
            $retryAfter = $responseDto->getRetryAfter();
            throw new MarketplaceRateLimitException(
                'Rate limit exceeded',
                $retryAfter
            );
        }

        if ($response->status() === 422) {
            throw new MarketplaceValidationException(
                'Validation failed',
                $response->json('errors', [])
            );
        }

        if (!$response->successful()) {
            throw new \RuntimeException(
                'API request failed: ' . ($response->json('message') ?? $response->body())
            );
        }

        return $responseDto;
    }

    /**
     * Mock methods for development
     */
    private function mockCreateListing(ListingDTO $dto): ResponseDTO
    {
        $this->logMockOperation('create_listing', $dto->toArray());

        return ResponseDTO::success([
            'id' => 'mock_listing_' . uniqid(),
            'title' => $dto->title,
            'status' => 'draft',
            'created_at' => now()->toISOString(),
        ]);
    }

    private function mockUpdateListing(string $externalId, ListingDTO $dto): ResponseDTO
    {
        $this->logMockOperation('update_listing', ['external_id' => $externalId] + $dto->toArray());

        return ResponseDTO::success([
            'id' => $externalId,
            'title' => $dto->title,
            'status' => 'draft',
            'updated_at' => now()->toISOString(),
        ]);
    }

    private function mockUploadImage(string $externalId, $file): ResponseDTO
    {
        $this->logMockOperation('upload_image', ['external_id' => $externalId]);

        return ResponseDTO::success([
            'image_id' => 'mock_image_' . uniqid(),
            'listing_id' => $externalId,
            'url' => 'https://example.com/mock-image.jpg',
            'uploaded_at' => now()->toISOString(),
        ]);
    }

    private function mockPublish(string $externalId): ResponseDTO
    {
        $this->logMockOperation('publish_listing', ['external_id' => $externalId]);

        return ResponseDTO::success([
            'id' => $externalId,
            'status' => 'published',
            'listing_url' => "https://marketplace.example.com/listings/{$externalId}",
            'published_at' => now()->toISOString(),
        ]);
    }

    private function mockUnpublish(string $externalId): ResponseDTO
    {
        $this->logMockOperation('unpublish_listing', ['external_id' => $externalId]);

        return ResponseDTO::success([
            'id' => $externalId,
            'status' => 'unpublished',
            'unpublished_at' => now()->toISOString(),
        ]);
    }

    /**
     * Logging helpers
     */
    private function logOperation(string $operation, array $context): void
    {
        Log::info("Marketplace API: {$operation}", [
            'tenant_id' => $this->tenantId,
            'operation' => $operation,
            'context' => $context,
            'correlation_id' => request()->header('X-Correlation-ID', uniqid()),
        ]);
    }

    private function logError(string $operation, \Exception $e, array $context = []): void
    {
        Log::error("Marketplace API Error: {$operation}", [
            'tenant_id' => $this->tenantId,
            'operation' => $operation,
            'error' => $e->getMessage(),
            'context' => $context,
            'correlation_id' => request()->header('X-Correlation-ID', uniqid()),
        ]);
    }

    private function logMockOperation(string $operation, array $context): void
    {
        Log::info("Marketplace API (MOCK): {$operation}", [
            'tenant_id' => $this->tenantId,
            'operation' => $operation,
            'context' => $context,
            'mock' => true,
        ]);
    }
}
