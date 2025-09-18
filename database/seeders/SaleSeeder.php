<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\Car;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run()
    {
        $cars = Car::limit(5)->get();
        $customers = Customer::limit(5)->get();
        
        if ($cars->count() < 3 || $customers->count() < 3) {
            echo "Niet genoeg auto's of klanten in database om sales aan te maken.\n";
            return;
        }
        
        // Test sale 1 - Actieve verkoop
        Sale::create([
            'car_id' => $cars->get(0)->id,
            'customer_id' => $customers->get(0)->id,
            'sale_price' => 25000,
            'deposit_amount' => 2500,
            'payment_status' => 'deposit_paid',
            'status' => 'contract_signed',
            'contract_signed_at' => Carbon::now()->subDays(2),
            'delivery_date' => Carbon::now()->addDays(5),
            'notes' => 'Klant wacht op financiering goedkeuring',
            'sold_at' => Carbon::now()->subDays(2),
        ]);

        // Test sale 2 - Actieve verkoop
        Sale::create([
            'car_id' => $cars->get(1)->id,
            'customer_id' => $customers->get(1)->id,
            'sale_price' => 18500,
            'deposit_amount' => 1850,
            'payment_status' => 'paid',
            'status' => 'ready_for_delivery',
            'contract_signed_at' => Carbon::now()->subDays(1),
            'delivery_date' => Carbon::now()->addDays(3),
            'notes' => 'Klaar voor oplevering',
            'sold_at' => Carbon::now()->subDays(1),
        ]);

        // Test sale 3 - Geleverd
        Sale::create([
            'car_id' => $cars->get(2)->id,
            'customer_id' => $customers->get(2)->id,
            'sale_price' => 32000,
            'deposit_amount' => 3200,
            'payment_status' => 'paid',
            'status' => 'delivered',
            'contract_signed_at' => Carbon::now()->subDays(10),
            'delivery_date' => Carbon::now()->subDays(3),
            'notes' => 'Succesvol opgeleverd',
            'sold_at' => Carbon::now()->subDays(10),
        ]);

        echo "Test sales toegevoegd!\n";
    }
}
