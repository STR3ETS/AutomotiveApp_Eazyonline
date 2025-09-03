<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarStagesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('car_stages')->insert([
            ['name' => 'Intake & Juridisch', 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Technische Intake', 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Commercieel Gereed', 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Proefrit & Verzekering', 'order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Verkoop Klaar', 'order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
