<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CarStage;

class CarsTableSeeder extends Seeder
{
    public function run()
    {
        // Haal alle stages op
        $stages = CarStage::orderBy('order')->get();
        $stageIds = $stages->pluck('id')->toArray();

        $cars = [
            [
                'license_plate' => 'AB-123-C',
                'brand' => 'Volkswagen',
                'model' => 'Golf',
                'year' => 2020,
                'mileage' => 25000,
                'price' => 18950.00,
                'status' => 'Intake',
                'stage_id' => $stageIds[0] ?? 1,
            ],
            [
                'license_plate' => 'XY-987-Z',
                'brand' => 'Peugeot',
                'model' => '208',
                'year' => 2019,
                'mileage' => 32000,
                'price' => 15400.00,
                'status' => 'Intake',
                'stage_id' => $stageIds[0] ?? 1,
            ],
            [
                'license_plate' => 'CD-456-F',
                'brand' => 'BMW',
                'model' => '320i',
                'year' => 2021,
                'mileage' => 18000,
                'price' => 28500.00,
                'status' => 'Technische controle',
                'stage_id' => $stageIds[1] ?? 1,
            ],
            [
                'license_plate' => 'GH-789-J',
                'brand' => 'Audi',
                'model' => 'A3',
                'year' => 2018,
                'mileage' => 45000,
                'price' => 22300.00,
                'status' => 'Technische controle',
                'stage_id' => $stageIds[1] ?? 1,
            ],
            [
                'license_plate' => 'KL-321-M',
                'brand' => 'Mercedes',
                'model' => 'C-Klasse',
                'year' => 2022,
                'mileage' => 12000,
                'price' => 35200.00,
                'status' => 'Herstel & Onderhoud',
                'stage_id' => $stageIds[2] ?? 1,
            ],
            [
                'license_plate' => 'NO-654-P',
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2020,
                'mileage' => 28000,
                'price' => 19800.00,
                'status' => 'Commercieel gereed',
                'stage_id' => $stageIds[3] ?? 1,
            ],
            [
                'license_plate' => 'QR-147-S',
                'brand' => 'Ford',
                'model' => 'Focus',
                'year' => 2019,
                'mileage' => 35000,
                'price' => 16900.00,
                'status' => 'Intake',
                'stage_id' => $stageIds[0] ?? 1,
            ],
            [
                'license_plate' => 'TU-258-V',
                'brand' => 'Opel',
                'model' => 'Corsa',
                'year' => 2021,
                'mileage' => 15000,
                'price' => 17500.00,
                'status' => 'Intake',
                'stage_id' => $stageIds[0] ?? 1,
            ],
            [
                'license_plate' => 'WX-369-Y',
                'brand' => 'Renault',
                'model' => 'Clio',
                'year' => 2020,
                'mileage' => 22000,
                'price' => 15800.00,
                'status' => 'Technische controle',
                'stage_id' => $stageIds[1] ?? 1,
            ],
            [
                'license_plate' => 'ZA-741-B',
                'brand' => 'Nissan',
                'model' => 'Qashqai',
                'year' => 2019,
                'mileage' => 38000,
                'price' => 21200.00,
                'status' => 'Technische controle',
                'stage_id' => $stageIds[1] ?? 1,
            ],
            [
                'license_plate' => 'BC-852-D',
                'brand' => 'Hyundai',
                'model' => 'i30',
                'year' => 2021,
                'mileage' => 16000,
                'price' => 18700.00,
                'status' => 'Herstel & Onderhoud',
                'stage_id' => $stageIds[2] ?? 1,
            ],
            [
                'license_plate' => 'EF-963-G',
                'brand' => 'Kia',
                'model' => 'Ceed',
                'year' => 2020,
                'mileage' => 24000,
                'price' => 17900.00,
                'status' => 'Herstel & Onderhoud',
                'stage_id' => $stageIds[2] ?? 1,
            ],
            [
                'license_plate' => 'HI-174-J',
                'brand' => 'Seat',
                'model' => 'Leon',
                'year' => 2019,
                'mileage' => 33000,
                'price' => 16500.00,
                'status' => 'Commercieel gereed',
                'stage_id' => $stageIds[3] ?? 1,
            ],
            [
                'license_plate' => 'KL-285-M',
                'brand' => 'Skoda',
                'model' => 'Octavia',
                'year' => 2021,
                'mileage' => 19000,
                'price' => 23400.00,
                'status' => 'Commercieel gereed',
                'stage_id' => $stageIds[3] ?? 1,
            ],
            [
                'license_plate' => 'NO-396-P',
                'brand' => 'Mazda',
                'model' => 'CX-5',
                'year' => 2020,
                'mileage' => 27000,
                'price' => 26800.00,
                'status' => 'Intake',
                'stage_id' => $stageIds[0] ?? 1,
            ],
            [
                'license_plate' => 'QR-417-S',
                'brand' => 'Honda',
                'model' => 'Civic',
                'year' => 2019,
                'mileage' => 31000,
                'price' => 19200.00,
                'status' => 'Intake',
                'stage_id' => $stageIds[0] ?? 1,
            ],
            [
                'license_plate' => 'TU-528-V',
                'brand' => 'Volvo',
                'model' => 'V40',
                'year' => 2018,
                'mileage' => 42000,
                'price' => 20500.00,
                'status' => 'Technische controle',
                'stage_id' => $stageIds[1] ?? 1,
            ],
            [
                'license_plate' => 'WX-639-Y',
                'brand' => 'Alfa Romeo',
                'model' => 'Giulietta',
                'year' => 2017,
                'mileage' => 48000,
                'price' => 17800.00,
                'status' => 'Herstel & Onderhoud',
                'stage_id' => $stageIds[2] ?? 1,
            ],
            [
                'license_plate' => 'ZA-741-B2',
                'brand' => 'Mini',
                'model' => 'Cooper',
                'year' => 2020,
                'mileage' => 21000,
                'price' => 24900.00,
                'status' => 'Commercieel gereed',
                'stage_id' => $stageIds[3] ?? 1,
            ],
            [
                'license_plate' => 'BC-852-D2',
                'brand' => 'Fiat',
                'model' => '500',
                'year' => 2019,
                'mileage' => 26000,
                'price' => 13500.00,
                'status' => 'Commercieel gereed',
                'stage_id' => $stageIds[3] ?? 1,
            ],
        ];

        foreach ($cars as $carData) {
            DB::table('cars')->updateOrInsert(
                ['license_plate' => $carData['license_plate']], // unique key
                array_merge($carData, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
