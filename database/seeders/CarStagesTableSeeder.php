<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarStagesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('car_stages')->insert([
            ['name' => 'Intake', 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Technische intake', 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Commercieel gereed', 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Proefrit & Verzekering', 'order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Verkoop & Aflevering', 'order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
