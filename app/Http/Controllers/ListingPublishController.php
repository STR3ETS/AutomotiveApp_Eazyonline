<?php

namespace App\Http\Controllers;

use App\Jobs\PublishListingJob;
use App\Models\Car;
use App\Services\Analytics\Ga4Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ListingPublishController extends Controller
{
    /**
     * Publish a car listing to marketplace
     */
    public function publishListing(Request $request, int $tenant, int $id)
    {
        try {
            // Find the car within tenant context
            $car = Car::where('company_id', $tenant)->findOrFail($id);

            // Validate that car is ready for publishing
            if (!$this->canPublishCar($car)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Auto kan niet gepubliceerd worden. Controleer of alle verplichte gegevens aanwezig zijn.'
                ], 400);
            }

            // Dispatch job to handle publishing
            PublishListingJob::dispatch($tenant, $id);

            Log::info('Listing publish job dispatched', [
                'tenant_id' => $tenant,
                'car_id' => $id,
                'license_plate' => $car->license_plate,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Publicatie taak is gestart. Je ontvangt een melding wanneer deze voltooid is.',
                'job_queued' => true
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auto niet gevonden.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to dispatch publish listing job', [
                'tenant_id' => $tenant,
                'car_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Er is een fout opgetreden bij het starten van de publicatie.'
            ], 500);
        }
    }

    /**
     * Get active users from GA4
     */
    public function getActiveUsers(Request $request, int $tenant)
    {
        try {
            $ga4Client = new Ga4Client($tenant);
            $result = $ga4Client->getActiveUsersWithCache();

            $response = response()->json([
                'success' => true,
                'active_users' => $result['active_users'],
                'is_cached' => $result['is_cached'],
                'last_updated' => $result['last_updated'],
            ]);

            // Add mock header if using mock data
            if ($result['is_mock']) {
                $response->header('X-Mock', 'true');
            }

            Log::info('Active users requested', [
                'tenant_id' => $tenant,
                'active_users' => $result['active_users'],
                'is_mock' => $result['is_mock'],
                'is_cached' => $result['is_cached'],
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('Failed to get active users', [
                'tenant_id' => $tenant,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kon actieve gebruikers niet ophalen.',
                'active_users' => 0,
            ])->header('X-Mock', 'true');
        }
    }

    /**
     * Get listing status for a car
     */
    public function getListingStatus(Request $request, int $tenant, int $id)
    {
        try {
            $car = Car::where('company_id', $tenant)
                     ->with(['listings' => function($query) {
                         $query->latest();
                     }])
                     ->findOrFail($id);

            $latestListing = $car->listings->first();

            return response()->json([
                'success' => true,
                'car_id' => $car->id,
                'license_plate' => $car->license_plate,
                'has_listing' => $latestListing !== null,
                'listing_status' => $latestListing?->status,
                'listing_url' => $latestListing?->listing_url,
                'external_id' => $latestListing?->external_id,
                'published_at' => $latestListing?->published_at,
                'can_publish' => $this->canPublishCar($car),
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auto niet gevonden.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to get listing status', [
                'tenant_id' => $tenant,
                'car_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kon listing status niet ophalen.'
            ], 500);
        }
    }

    /**
     * Check if car can be published
     */
    private function canPublishCar(Car $car): bool
    {
        // Basic validation - extend as needed
        if (empty($car->license_plate) || empty($car->brand) || empty($car->model)) {
            return false;
        }

        if ($car->price <= 0) {
            return false;
        }

        if ($car->year < 1950 || $car->year > date('Y') + 1) {
            return false;
        }

        // Check if car is in a publishable stage
        $publishableStages = ['Commercieel gereed', 'Verkoop klaar'];
        if (!in_array($car->status, $publishableStages)) {
            return false;
        }

        return true;
    }
}
