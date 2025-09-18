<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Services\CarSaleService;
use Illuminate\Console\Command;

class TestCarSaleMovement extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:car-sale-movement {sale_id?}';

    /**
     * The console command description.
     */
    protected $description = 'Test the automatic movement of cars to sold_cars table when payment is completed';

    protected $carSaleService;

    public function __construct(CarSaleService $carSaleService)
    {
        parent::__construct();
        $this->carSaleService = $carSaleService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $saleId = $this->argument('sale_id');
        
        if ($saleId) {
            $this->testSpecificSale($saleId);
        } else {
            $this->testAutomaticFlow();
        }
    }

    /**
     * Test specific sale by ID
     */
    protected function testSpecificSale($saleId)
    {
        $sale = Sale::with(['car', 'customer'])->find($saleId);
        
        if (!$sale) {
            $this->error("Sale with ID {$saleId} not found!");
            return;
        }

        $this->info("Testing sale ID: {$sale->id}");
        $this->info("Car: {$sale->car->brand} {$sale->car->model} ({$sale->car->license_plate})");
        $this->info("Customer: {$sale->customer->name}");
        $this->info("Sale price: €" . number_format($sale->sale_price, 2));
        $this->info("Deposit: €" . number_format($sale->deposit_amount ?? 0, 2));
        $this->info("Payment status: {$sale->payment_status}");
        
        $remaining = $this->carSaleService->getRemainingBalance($sale);
        $this->info("Remaining balance: €" . number_format($remaining, 2));

        if ($sale->payment_status === 'paid') {
            $this->warn("This sale is already marked as paid. The car should already be moved to sold_cars.");
            return;
        }

        if ($this->confirm('Mark this sale as fully paid and trigger the automatic movement?')) {
            try {
                // This will trigger the observer
                $sale->update(['payment_status' => 'paid']);
                
                $this->info("✅ Sale marked as paid! The observer should have moved the car to sold_cars.");
                $this->info("Check the logs for details about the movement process.");
                
            } catch (\Exception $e) {
                $this->error("❌ Error updating sale: " . $e->getMessage());
            }
        }
    }

    /**
     * Test automatic flow with sample data
     */
    protected function testAutomaticFlow()
    {
        $this->info("Looking for sales that can be tested...");
        
        // Find sales that are not yet fully paid
        $sales = Sale::with(['car', 'customer'])
            ->where('payment_status', '!=', 'paid')
            ->whereHas('car')
            ->whereHas('customer')
            ->limit(5)
            ->get();

        if ($sales->isEmpty()) {
            $this->warn("No suitable sales found for testing. All sales are either already paid or missing car/customer data.");
            return;
        }

        $this->table(
            ['ID', 'Car', 'Customer', 'Sale Price', 'Deposit', 'Remaining', 'Payment Status'],
            $sales->map(function ($sale) {
                $remaining = $this->carSaleService->getRemainingBalance($sale);
                return [
                    $sale->id,
                    $sale->car->brand . ' ' . $sale->car->model,
                    $sale->customer->name,
                    '€' . number_format($sale->sale_price, 2),
                    '€' . number_format($sale->deposit_amount ?? 0, 2),
                    '€' . number_format($remaining, 2),
                    $sale->payment_status
                ];
            })
        );

        $saleId = $this->ask('Enter the ID of the sale you want to test (or press Enter to cancel)');
        
        if ($saleId && is_numeric($saleId)) {
            $this->testSpecificSale($saleId);
        } else {
            $this->info('Test cancelled.');
        }
    }
}
