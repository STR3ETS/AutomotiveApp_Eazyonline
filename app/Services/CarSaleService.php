<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Car;
use App\Models\SoldCar;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CarSaleService
{
    /**
     * Move a car from active inventory to sold_cars table when sale is fully paid
     */
    public function moveCarToSoldCars(Sale $sale): SoldCar
    {
        return DB::transaction(function () use ($sale) {
            // Load necessary relationships
            $sale->load(['car', 'customer']);
            
            if (!$sale->car) {
                throw new \Exception("Car not found for sale ID {$sale->id}");
            }
            
            if (!$sale->customer) {
                throw new \Exception("Customer not found for sale ID {$sale->id}");
            }

            // Create sold car record
            $soldCar = $this->createSoldCarRecord($sale);
            
            // Archive related data (optional - keep for now)
            $this->archiveRelatedData($sale);
            
            // Delete original records
            $this->deleteOriginalRecords($sale);
            
            Log::info('Car successfully moved to sold_cars', [
                'original_car_id' => $sale->car->id,
                'sold_car_id' => $soldCar->id,
                'license_plate' => $soldCar->license_plate
            ]);
            
            return $soldCar;
        });
    }

    /**
     * Create a sold car record with all necessary data
     */
    protected function createSoldCarRecord(Sale $sale): SoldCar
    {
        $car = $sale->car;
        $customer = $sale->customer;

        return SoldCar::create([
            // Original car information
            'license_plate' => $car->license_plate,
            'brand' => $car->brand,
            'model' => $car->model,
            'year' => $car->year,
            'mileage' => $car->mileage,
            'original_price' => $car->price,
            'purchase_price' => $car->purchase_price,
            
            // Sale information
            'sale_price' => $sale->sale_price,
            'deposit_amount' => $sale->deposit_amount,
            'sold_at' => $sale->sold_at ?? now(),
            'delivery_date' => $sale->delivery_date,
            
            // Customer information
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'customer_address' => $customer->address,
            
            // Additional information
            'notes' => $sale->notes,
            'images' => $this->getCarImages($car),
            
            // Reference IDs for history tracking
            'original_car_id' => $car->id,
            'original_sale_id' => $sale->id,
            'original_customer_id' => $customer->id,
            
            // Company relation
            'company_id' => $sale->company_id,
        ]);
    }

    /**
     * Get car images as JSON array
     */
    protected function getCarImages(Car $car): ?array
    {
        // Check if car has images relationship
        if (method_exists($car, 'images') && $car->hasImages()) {
            return $car->images()->get()->map(function ($image) {
                return [
                    'filename' => $image->filename,
                    'url' => $image->url,
                    'thumbnail_url' => $image->thumbnail_url,
                    'category' => $image->category,
                    'is_primary' => $image->is_primary,
                    'alt_text' => $image->alt_text,
                ];
            })->toArray();
        }
        
        return null;
    }

    /**
     * Archive related data before deletion (optional)
     */
    protected function archiveRelatedData(Sale $sale): void
    {
        // For now, we'll keep related data like appointments and repairs
        // They might still be useful for reference even after the car is sold
        
        // Future: We could create archive tables or soft delete these records
        Log::info('Related data preserved for sold car', [
            'car_id' => $sale->car->id,
            'sale_id' => $sale->id
        ]);
    }

    /**
     * Delete original records after successful archiving
     */
    protected function deleteOriginalRecords(Sale $sale): void
    {
        $carId = $sale->car->id;
        
        // Delete checklist items first (due to foreign key constraints)
        $sale->checklistItems()->delete();
        
        // Delete the sale record
        $sale->delete();
        
        // Delete the car record (this will cascade to related records)
        Car::destroy($carId);
        
        Log::info('Original records deleted', [
            'car_id' => $carId,
            'sale_id' => $sale->id
        ]);
    }

    /**
     * Check if a sale is eligible for car archiving
     */
    public function isSaleEligibleForArchiving(Sale $sale): bool
    {
        return $sale->payment_status === 'paid' && 
               $sale->status === 'delivered' &&
               $sale->car()->exists();
    }

    /**
     * Get remaining balance for a sale
     */
    public function getRemainingBalance(Sale $sale): float
    {
        return max(0, $sale->sale_price - ($sale->deposit_amount ?? 0));
    }

    /**
     * Check if sale is fully paid
     */
    public function isFullyPaid(Sale $sale): bool
    {
        return $this->getRemainingBalance($sale) <= 0.01; // Allow for small rounding differences
    }
}
