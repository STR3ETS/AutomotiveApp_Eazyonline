<?php

namespace Database\Seeders;

use App\Models\Repair;
use App\Models\Car;
use Illuminate\Database\Seeder;

class RepairSeeder extends Seeder
{
    public function run()
    {
        $cars = Car::limit(3)->get();
        
        if ($cars->count() < 2) {
            echo "Niet genoeg auto's in database om reparaties aan te maken.\n";
            return;
        }
        
        // Test reparatie 1
        Repair::create([
            'car_id' => $cars->first()->id,
            'description' => 'Remmen vervangen',
            'status' => 'gepland',
            'cost_estimate' => 350.00
        ]);

        // Test reparatie 2
        Repair::create([
            'car_id' => $cars->get(1)->id,
            'description' => 'Airco service',
            'status' => 'bezig',
            'cost_estimate' => 150.00
        ]);

        // Test reparatie 3
        if ($cars->count() > 2) {
            Repair::create([
                'car_id' => $cars->get(2)->id,
                'description' => 'Banden vervangen',
                'status' => 'wachten_op_onderdeel',
                'cost_estimate' => 400.00
            ]);
        }

        echo "Test reparaties toegevoegd!\n";
    }
}
