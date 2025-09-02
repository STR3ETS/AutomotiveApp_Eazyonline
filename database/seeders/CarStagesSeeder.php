<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarStagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Intake',
                'order' => 1,
                'description' => 'Auto opname en eerste controle'
            ],
            [
                'name' => 'Technische controle',
                'order' => 2,
                'description' => 'APK, onderhoud en technische inspectie'
            ],
            [
                'name' => 'Herstel & Onderhoud',
                'order' => 3,
                'description' => 'Reparaties en onderhoudswerkzaamheden'
            ],
            [
                'name' => 'Commercieel gereed',
                'order' => 4,
                'description' => 'Auto klaarmaken voor verkoop'
            ],
            [
                'name' => 'Verkoop klaar',
                'order' => 5,
                'description' => 'Klaar voor verkoop en proefritten'
            ]
        ];

        foreach ($stages as $stage) {
            DB::table('car_stages')->updateOrInsert(
                ['name' => $stage['name']],
                [
                    'order' => $stage['order'],
                    'description' => $stage['description'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
