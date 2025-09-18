<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\Car;
use App\Models\Customer;
use Illuminate\Console\Command;

class CreateTestSale extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:create-sale';

    /**
     * The console command description.
     */
    protected $description = 'Create a test sale for testing redirect functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $car = Car::first();
        $customer = Customer::first();
        
        if (!$car || !$customer) {
            $this->error('No car or customer found in database!');
            return 1;
        }

        $sale = Sale::create([
            'car_id' => $car->id,
            'customer_id' => $customer->id,
            'sale_price' => 15000,
            'deposit_amount' => 1000,
            'payment_status' => 'deposit_paid',
            'status' => 'contract_signed',
            'company_id' => $car->company_id,
        ]);

        $this->info("✅ Test sale created with ID: {$sale->id}");
        $this->info("Car: {$car->brand} {$car->model} ({$car->license_plate})");
        $this->info("Customer: {$customer->name}");
        $this->info("Sale price: €" . number_format($sale->sale_price, 2));
        $this->info("Deposit: €" . number_format($sale->deposit_amount, 2));
        $this->info("Remaining: €" . number_format($sale->sale_price - $sale->deposit_amount, 2));

        return 0;
    }
}
