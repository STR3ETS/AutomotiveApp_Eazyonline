<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\SoldCar;
use Illuminate\Console\Command;

class CheckSalesStatus extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'check:sales-status';

    /**
     * The console command description.
     */
    protected $description = 'Check the current status of sales and sold cars';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== SALES STATUS CHECK ===');
        
        // Sales statistics
        $totalSales = Sale::count();
        $this->info("Total active sales: {$totalSales}");
        
        if ($totalSales > 0) {
            $this->info("\nSales by payment status:");
            Sale::selectRaw('payment_status, count(*) as count')
                ->groupBy('payment_status')
                ->get()
                ->each(function ($status) {
                    $this->line("  {$status->payment_status}: {$status->count}");
                });
            
            $this->info("\nSales details:");
            Sale::with(['car', 'customer'])->get()->each(function ($sale) {
                $car = $sale->car ? "{$sale->car->brand} {$sale->car->model}" : 'No car';
                $customer = $sale->customer ? $sale->customer->name : 'No customer';
                $this->line("  Sale {$sale->id}: {$car} -> {$customer} (Payment: {$sale->payment_status})");
            });
        }
        
        // Sold cars statistics
        $totalSoldCars = SoldCar::count();
        $this->info("\n=== SOLD CARS ===");
        $this->info("Total sold cars: {$totalSoldCars}");
        
        if ($totalSoldCars > 0) {
            SoldCar::latest('sold_at')->take(5)->get()->each(function ($soldCar) {
                $this->line("  {$soldCar->brand} {$soldCar->model} ({$soldCar->license_plate}) - Sold: {$soldCar->sold_at->format('Y-m-d')}");
            });
        }
        
        return 0;
    }
}
