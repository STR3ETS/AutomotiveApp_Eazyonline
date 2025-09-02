<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\DeliveryChecklistItem;

class SalesSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::firstOrCreate([
            'email' => 'jan@example.com'
        ], [
            'name' => 'Jan Jansen',
            'phone' => '0612345678',
            'address' => 'Dorpsstraat 1, 1234 AB, Arnhem'
        ]);

        $car = Car::firstOrCreate([
            'license_plate' => 'XX-YY-99'
        ], [
            'brand' => 'Volkswagen',
            'model' => 'Golf',
            'year' => 2018,
            'mileage' => 60000,
            'price' => 14500,
            'status' => 'Verkoop klaar'
        ]);

        $sale = Sale::create([
            'car_id' => $car->id,
            'customer_id' => $customer->id,
            'sale_price' => 13950,
            'deposit_amount' => 500,
            'payment_status' => 'deposit_paid',
            'status' => 'contract_signed',
        ]);

        $tasks = [
            'APK/Technische check afgerond',
            'Interieur + exterieur poetsen',
            'Kentekenplaten gemonteerd',
            'Sleutels + reservesleutel aanwezig',
            'RDW vrijwaring geregeld',
            'Verzekering & groene kaart bevestigd',
            'Contract ondertekend en gedocumenteerd',
        ];

        foreach ($tasks as $task) {
            DeliveryChecklistItem::create([
                'sale_id' => $sale->id,
                'task' => $task,
                'is_completed' => false,
            ]);
        }
    }
}