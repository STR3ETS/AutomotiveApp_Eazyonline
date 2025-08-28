<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarsTableSeeder extends Seeder
{
    public function run()
    {
        // Haal de eerste stage op
        $stageId = DB::table('car_stages')->orderBy('order')->first()->id ?? 1;

        DB::table('cars')->insert([
            [
                'license_plate' => 'AB-123-C',
                'brand' => 'Volkswagen',
                'model' => 'Golf',
                'year' => 2020,
                'mileage' => 25000,
                'price' => 18950.00,
                'status' => 'intake',
                'stage_id' => $stageId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'license_plate' => 'XY-987-Z',
                'brand' => 'Peugeot',
                'model' => '208',
                'year' => 2019,
                'mileage' => 32000,
                'price' => 15400.00,
                'status' => 'intake',
                'stage_id' => $stageId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
