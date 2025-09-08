<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Car;
use App\Models\Customer;
use App\Models\Appointment;
use App\Models\Repair;
use App\Models\Part;
use App\Models\Sale;
use App\Models\CarStage;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create car stages first (shared across companies) - CORRECTE STAGES
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

        foreach ($stages as $stageData) {
            CarStage::firstOrCreate(['name' => $stageData['name']], $stageData);
        }

        // Company 1: AutoGarage Piet
        $company1 = Company::create([
            'name' => 'AutoGarage Piet',
            'subdomain' => 'piet',
            'primary_color' => '#dc2626', // Red
            'active' => true,
        ]);

        $this->seedCompanyData($company1, [
            'user_name' => 'Piet van der Berg',
            'user_email' => 'piet@autogaragepiet.nl',
            'cars_count' => 8,
            'customers_count' => 12,
        ]);

        // Company 2: De Snelle Garage
        $company2 = Company::create([
            'name' => 'De Snelle Garage',
            'subdomain' => 'snelle',
            'primary_color' => '#059669', // Green
            'active' => true,
        ]);

        $this->seedCompanyData($company2, [
            'user_name' => 'Mark Jansen',
            'user_email' => 'mark@desnellegarage.nl',
            'cars_count' => 6,
            'customers_count' => 8,
        ]);

        // Company 3: Premium Motors
        $company3 = Company::create([
            'name' => 'Premium Motors',
            'subdomain' => 'premium',
            'primary_color' => '#7c3aed', // Purple
            'active' => true,
        ]);

        $this->seedCompanyData($company3, [
            'user_name' => 'Lisa de Vries',
            'user_email' => 'lisa@premiummotors.nl',
            'cars_count' => 12,
            'customers_count' => 15,
        ]);

        // Company 4: Buurgarage Jan
        $company4 = Company::create([
            'name' => 'Buurgarage Jan',
            'subdomain' => 'buurgarage',
            'primary_color' => '#ea580c', // Orange
            'active' => true,
        ]);

        $this->seedCompanyData($company4, [
            'user_name' => 'Jan Bakker',
            'user_email' => 'jan@buurgaragejan.nl',
            'cars_count' => 4,
            'customers_count' => 6,
        ]);

        echo "✅ Created 4 companies with users, cars, customers, and appointments!\n";
    }

    private function seedCompanyData(Company $company, array $config)
    {
        // Create admin user for company
        $user = User::create([
            'name' => $config['user_name'],
            'email' => $config['user_email'],
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'company_id' => $company->id,
        ]);

        // Create customers
        $customers = [];
        $customerNames = [
            'Henk de Jong', 'Maria Vermeulen', 'Kees van Dijk', 'Anna Smit',
            'Tom Willems', 'Sophie Mulder', 'Rob Peters', 'Linda Koning',
            'Peter van Leeuwen', 'Sandra Visser', 'Michel de Wit', 'Carla Jacobs',
            'Frank Bos', 'Nicole van Dam', 'Erik Brouwer', 'Marloes Dekker'
        ];

        for ($i = 0; $i < $config['customers_count']; $i++) {
            $customers[] = Customer::create([
                'name' => $customerNames[$i] ?? "Klant " . ($i + 1),
                'email' => strtolower(str_replace(' ', '.', $customerNames[$i] ?? "klant{$i}")) . '@example.com',
                'phone' => '06' . rand(10000000, 99999999),
                'address' => 'Voorbeeldstraat ' . rand(1, 100) . ', 1234AB Voorbeeldstad',
                'company_id' => $company->id,
            ]);
        }

        // Create cars with different statuses
        $carData = [
            ['brand' => 'Volkswagen', 'model' => 'Golf', 'year' => 2019, 'mileage' => 75000, 'price' => 18500],
            ['brand' => 'BMW', 'model' => '3 Serie', 'year' => 2020, 'mileage' => 45000, 'price' => 28000],
            ['brand' => 'Audi', 'model' => 'A4', 'year' => 2018, 'mileage' => 85000, 'price' => 22000],
            ['brand' => 'Mercedes', 'model' => 'C-Klasse', 'year' => 2021, 'mileage' => 25000, 'price' => 35000],
            ['brand' => 'Toyota', 'model' => 'Corolla', 'year' => 2019, 'mileage' => 65000, 'price' => 16000],
            ['brand' => 'Ford', 'model' => 'Focus', 'year' => 2017, 'mileage' => 95000, 'price' => 12000],
            ['brand' => 'Opel', 'model' => 'Astra', 'year' => 2020, 'mileage' => 35000, 'price' => 19000],
            ['brand' => 'Peugeot', 'model' => '308', 'year' => 2018, 'mileage' => 70000, 'price' => 15000],
            ['brand' => 'Renault', 'model' => 'Megane', 'year' => 2019, 'mileage' => 55000, 'price' => 17000],
            ['brand' => 'Skoda', 'model' => 'Octavia', 'year' => 2020, 'mileage' => 40000, 'price' => 21000],
            ['brand' => 'Nissan', 'model' => 'Qashqai', 'year' => 2018, 'mileage' => 80000, 'price' => 18000],
            ['brand' => 'Honda', 'model' => 'Civic', 'year' => 2019, 'mileage' => 60000, 'price' => 19500],
        ];

        $stages = CarStage::all();
        $cars = [];

        for ($i = 0; $i < $config['cars_count']; $i++) {
            $carInfo = $carData[$i % count($carData)];
            $stage = $stages->random();
            
            $cars[] = Car::create([
                'license_plate' => $this->generateLicensePlate(),
                'brand' => $carInfo['brand'],
                'model' => $carInfo['model'],
                'year' => $carInfo['year'],
                'mileage' => $carInfo['mileage'] + rand(-10000, 20000),
                'price' => $carInfo['price'] + rand(-2000, 5000),
                'status' => $stage->name,
                'stage_id' => $stage->id,
                'company_id' => $company->id,
            ]);
        }

        // Create appointments
        foreach (array_slice($cars, 0, 3) as $car) {
            Appointment::create([
                'car_id' => $car->id,
                'customer_id' => $customers[array_rand($customers)]->id,
                'type' => ['proefrit', 'aflevering', 'werkplaats'][rand(0, 2)],
                'date' => now()->addDays(rand(1, 30))->format('Y-m-d'),
                'time' => rand(8, 17) . ':' . ['00', '30'][rand(0, 1)],
                'notes' => 'Afspraak voor ' . $car->brand . ' ' . $car->model,
                'company_id' => $company->id,
            ]);
        }

        // Create repairs for some cars
        foreach (array_slice($cars, 0, 2) as $car) {
            $repair = Repair::create([
                'car_id' => $car->id,
                'description' => 'Reparatie aan ' . $car->brand . ' ' . $car->model,
                'status' => ['gepland', 'bezig', 'wachten_op_onderdeel', 'gereed'][rand(0, 3)],
                'cost_estimate' => rand(200, 1500),
                'company_id' => $company->id,
            ]);

            // Add some parts to repairs
            Part::create([
                'repair_id' => $repair->id,
                'name' => 'Remblokken',
                'status' => 'besteld',
                'price' => rand(50, 200),
                'company_id' => $company->id,
            ]);

            Part::create([
                'repair_id' => $repair->id,
                'name' => 'Motorolie',
                'status' => 'geleverd',
                'price' => rand(30, 80),
                'company_id' => $company->id,
            ]);
        }

        // Create some sales
        if (count($cars) > 0) {
            Sale::create([
                'car_id' => $cars[0]->id,
                'customer_id' => $customers[0]->id,
                'sale_price' => $cars[0]->price + rand(-1000, 2000),
                'deposit_amount' => 1000,
                'payment_status' => 'paid',
                'status' => 'delivered',
                'contract_signed_at' => now()->subDays(5),
                'delivery_date' => now()->addDays(3)->format('Y-m-d'),
                'delivery_time' => '14:00',
                'company_id' => $company->id,
            ]);
        }
    }

    private function generateLicensePlate(): string
    {
        $formats = [
            // Moderne Nederlandse kentekens
            function() { return rand(1, 9) . '-' . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . '-' . rand(1, 9); },
            function() { return chr(rand(65, 90)) . chr(rand(65, 90)) . '-' . rand(100, 999) . '-' . chr(rand(65, 90)); },
            function() { return rand(10, 99) . '-' . chr(rand(65, 90)) . chr(rand(65, 90)) . '-' . rand(10, 99); },
        ];
        
        return $formats[array_rand($formats)]();
    }
}
