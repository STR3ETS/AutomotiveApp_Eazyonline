<?php

namespace App\Observers;

use App\Models\Sale;
use App\Services\CarSaleService;
use Illuminate\Support\Facades\Log;

class SaleObserver
{
    protected $carSaleService;

    public function __construct(CarSaleService $carSaleService)
    {
        $this->carSaleService = $carSaleService;
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale)
    {
        // Check if payment_status changed to 'paid' AND the sale is actually fully paid
        if ($sale->isDirty('payment_status') && $sale->payment_status === 'paid') {

            // Double check if the sale is actually fully paid
            if (!$this->carSaleService->isFullyPaid($sale)) {
                Log::warning('Sale marked as paid but not fully paid', [
                    'sale_id' => $sale->id,
                    'sale_price' => $sale->sale_price,
                    'deposit_amount' => $sale->deposit_amount ?? 0,
                    'remaining' => $this->carSaleService->getRemainingBalance($sale)
                ]);
                return;
            }

            Log::info('Sale payment completed, moving car to sold_cars', [
                'sale_id' => $sale->id,
                'car_id' => $sale->car_id,
                'customer_id' => $sale->customer_id
            ]);

            try {
                $this->carSaleService->moveCarToSoldCars($sale);

                Log::info('Car successfully moved to sold_cars', [
                    'sale_id' => $sale->id,
                    'car_id' => $sale->car_id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to move car to sold_cars', [
                    'sale_id' => $sale->id,
                    'car_id' => $sale->car_id,
                    'error' => $e->getMessage()
                ]);

                // We could optionally throw the exception to prevent the sale update
                // throw $e;
            }
        }
    }
}
