<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->seedEmployeesForCompany($company);
        }

        echo "✅ Employees created for all companies!\n";
    }

    private function seedEmployeesForCompany(Company $company): void
    {
        // Employee data templates
        $employeeTemplates = [
            [
                'name' => 'Marco van der Berg', 
                'position' => 'Hoofdmonteur', 
                'specializations' => ['APK', 'Diagnose', 'Motor']
            ],
            [
                'name' => 'Dennis Janssen', 
                'position' => 'Monteur', 
                'specializations' => ['Remmen', 'Uitlaat', 'Banden']
            ],
            [
                'name' => 'Kevin Smit', 
                'position' => 'APK-keurder', 
                'specializations' => ['APK', 'Controle']
            ],
            [
                'name' => 'Roy Bakker', 
                'position' => 'Leerling', 
                'specializations' => ['Onderhoud', 'Controle']
            ],
            [
                'name' => 'Patrick de Vries', 
                'position' => 'Monteur', 
                'specializations' => ['Carrosserie', 'Lakwerk']
            ],
        ];

        // Determine how many employees based on company size
        $employeeCount = match($company->subdomain) {
            'piet' => 4,
            'snelle' => 3,
            'premium' => 5,
            'buurgarage' => 2,
            default => 3,
        };

        for ($i = 0; $i < $employeeCount; $i++) {
            $template = $employeeTemplates[$i];
            
            // Personalize name for each company
            $firstName = explode(' ', $template['name'])[0];
            $lastName = explode(' ', $template['name'])[1] ?? 'van der Berg';
            
            // Create unique names per company
            $uniqueName = match($company->subdomain) {
                'piet' => $template['name'],
                'snelle' => $firstName . ' Jansen',
                'premium' => $firstName . ' de Vries', 
                'buurgarage' => $firstName . ' Bakker',
                default => $template['name'],
            };

            Employee::firstOrCreate(
                [
                    'name' => $uniqueName,
                    'company_id' => $company->id
                ],
                [
                    'email' => strtolower(str_replace(' ', '.', $uniqueName)) . '@' . $company->subdomain . '.nl',
                    'phone' => '06' . rand(10000000, 99999999),
                    'position' => $template['position'],
                    'specializations' => $template['specializations'],
                    'active' => true,
                ]
            );
        }

        echo "   Created {$employeeCount} employees for {$company->name}\n";
    }
}
