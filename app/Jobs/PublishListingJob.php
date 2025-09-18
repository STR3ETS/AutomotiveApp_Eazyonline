<?php

namespace App\Jobs;

use App\Models\Car;
use App\Models\CarListing;
use App\DTO\ListingDTO;
use App\Services\Marketplace\MarketplaceClient;
use App\Repositories\ApiIntegrationRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishListingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $maxExceptions = 3;
    public int $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $tenantId,
        public int $localListingId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        MarketplaceClient $marketplaceClient,
        ApiIntegrationRepository $tokenRepository
    ): void {
        $correlationId = uniqid('publish_', true);
        
        Log::info('Starting listing publication job', [
            'tenant_id' => $this->tenantId,
            'car_id' => $this->localListingId,
            'correlation_id' => $correlationId,
            'attempt' => $this->attempts(),
        ]);

        try {
            // Find the car
            $car = Car::where('company_id', $this->tenantId)->findOrFail($this->localListingId);

            // Check if we have valid marketplace integration
            if (!$tokenRepository->hasValidIntegration($this->tenantId, 'marketplace')) {
                throw new \RuntimeException('No valid marketplace integration found for tenant');
            }

            // Create marketplace client with tenant context
            $client = new MarketplaceClient($tokenRepository, config('services.marketplace.base_url'), $this->tenantId);

            // Prepare listing data
            $listingDto = $this->prepareListingFromCar($car);

            // Create or update existing listing
            $carListing = $this->findOrCreateCarListing($car);

            if ($carListing->external_id) {
                // Update existing listing
                Log::info('Updating existing marketplace listing', [
                    'external_id' => $carListing->external_id,
                    'correlation_id' => $correlationId,
                ]);

                $response = $client->updateListing($carListing->external_id, $listingDto);
            } else {
                // Create new listing
                Log::info('Creating new marketplace listing', [
                    'correlation_id' => $correlationId,
                ]);

                $response = $client->createListing($listingDto);
                
                if ($response->success) {
                    $carListing->external_id = $response->getData('id');
                    $carListing->save();
                }
            }

            if (!$response->success) {
                throw new \RuntimeException('Failed to create/update listing: ' . $response->error);
            }

            // Upload images
            $this->uploadImages($client, $carListing, $car, $correlationId);

            // Publish the listing
            if ($carListing->external_id) {
                Log::info('Publishing marketplace listing', [
                    'external_id' => $carListing->external_id,
                    'correlation_id' => $correlationId,
                ]);

                $publishResponse = $client->publish($carListing->external_id);

                if ($publishResponse->success) {
                    $carListing->markAsPublished(
                        $carListing->external_id,
                        $publishResponse->getData('listing_url')
                    );

                    Log::info('Listing published successfully', [
                        'external_id' => $carListing->external_id,
                        'listing_url' => $publishResponse->getData('listing_url'),
                        'correlation_id' => $correlationId,
                    ]);
                } else {
                    throw new \RuntimeException('Failed to publish listing: ' . $publishResponse->error);
                }
            }

        } catch (\Exception $e) {
            Log::error('Listing publication job failed', [
                'tenant_id' => $this->tenantId,
                'car_id' => $this->localListingId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'correlation_id' => $correlationId,
            ]);

            // Mark listing as error if it exists
            if (isset($carListing)) {
                $carListing->markAsError($e->getMessage());
            }

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Listing publication job permanently failed', [
            'tenant_id' => $this->tenantId,
            'car_id' => $this->localListingId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Mark listing as permanently failed
        try {
            $car = Car::where('company_id', $this->tenantId)->find($this->localListingId);
            if ($car) {
                $carListing = $this->findOrCreateCarListing($car);
                $carListing->markAsError('Job permanently failed: ' . $exception->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Failed to mark listing as error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Calculate retry delay with exponential backoff
     */
    public function backoff(): array
    {
        return [30, 60, 120, 300, 600]; // 30s, 1m, 2m, 5m, 10m
    }

    /**
     * Prepare listing DTO from car model
     */
    private function prepareListingFromCar(Car $car): ListingDTO
    {
        $title = "{$car->brand} {$car->model} ({$car->year})";
        
        $description = "Te koop: {$car->brand} {$car->model} uit {$car->year}\n\n";
        $description .= "Kilometerstand: " . number_format($car->mileage, 0, ',', '.') . " km\n";
        $description .= "Kenteken: {$car->license_plate}\n\n";
        
        if (!empty($car->notes)) {
            $description .= "Opmerkingen:\n{$car->notes}";
        }

        $images = [];
        if ($car->hasImages()) {
            $images = $car->images->map(function ($image) {
                return $image->url;
            })->toArray();
        }

        return new ListingDTO(
            title: $title,
            description: $description,
            priceCents: (int) ($car->price * 100),
            currency: 'EUR',
            condition: 'used',
            category: 'cars',
            attributes: [
                'brand' => $car->brand,
                'model' => $car->model,
                'year' => $car->year,
                'mileage' => $car->mileage,
                'license_plate' => $car->license_plate,
            ],
            images: $images
        );
    }

    /**
     * Find or create car listing record
     */
    private function findOrCreateCarListing(Car $car): CarListing
    {
        return CarListing::firstOrCreate(
            [
                'car_id' => $car->id,
                'company_id' => $this->tenantId,
                'platform' => 'marketplace',
            ],
            [
                'listing_template_id' => 1, // Default template - adjust as needed
                'status' => 'pending',
                'generated_title' => "{$car->brand} {$car->model} ({$car->year})",
                'generated_description' => "Auto listing voor {$car->license_plate}",
            ]
        );
    }

    /**
     * Upload images for the listing
     */
    private function uploadImages(MarketplaceClient $client, CarListing $carListing, Car $car, string $correlationId): void
    {
        if (!$carListing->external_id || !$car->hasImages()) {
            return;
        }

        $uploadedImages = [];

        foreach ($car->images->take(10) as $image) { // Limit to 10 images
            try {
                Log::info('Uploading image to marketplace', [
                    'external_id' => $carListing->external_id,
                    'image_id' => $image->id,
                    'filename' => $image->filename,
                    'correlation_id' => $correlationId,
                ]);

                $imagePath = storage_path('app/public/car-images/' . $image->filename);
                
                if (file_exists($imagePath)) {
                    $response = $client->uploadImage($carListing->external_id, $imagePath);
                    
                    if ($response->success) {
                        $uploadedImages[] = $response->getData('image_id');
                        Log::info('Image uploaded successfully', [
                            'image_id' => $image->id,
                            'marketplace_image_id' => $response->getData('image_id'),
                            'correlation_id' => $correlationId,
                        ]);
                    } else {
                        Log::warning('Failed to upload image', [
                            'image_id' => $image->id,
                            'error' => $response->error,
                            'correlation_id' => $correlationId,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Image upload exception', [
                    'image_id' => $image->id,
                    'error' => $e->getMessage(),
                    'correlation_id' => $correlationId,
                ]);
                // Continue with other images
            }
        }

        // Update listing with uploaded images
        if (!empty($uploadedImages)) {
            $carListing->update(['used_images' => $uploadedImages]);
        }
    }
}
