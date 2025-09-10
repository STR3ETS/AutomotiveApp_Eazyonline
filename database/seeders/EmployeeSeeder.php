<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
        // Create owner first
        $this->createOwner($company);
        
        // Create medewerkers
        $employeeTemplates = $this->getEmployeeTemplatesForCompany($company->subdomain);

        // Determine how many medewerkers based on company size
        $employeeCount = match($company->subdomain) {
            'piet' => 3,
            'snelle' => 2,
            'premium' => 4,
            'buurgarage' => 2,
            default => 2,
        };

        for ($i = 0; $i < $employeeCount; $i++) {
            if (isset($employeeTemplates[$i])) {
                $this->createMedewerker($company, $employeeTemplates[$i]);
            }
        }

        $totalEmployees = $employeeCount + 1;
        echo "   Created {$totalEmployees} employees for {$company->name}\n";
    }

    private function getEmployeeTemplatesForCompany(string $subdomain): array
    {
        return match($subdomain) {
            'piet' => [
                ['name' => 'Marco van der Berg'],
                ['name' => 'Dennis Janssen'],
                ['name' => 'Kevin de Wit'],
            ],
            'snelle' => [
                ['name' => 'Sven Jansen'],
                ['name' => 'Rick Vermeer'],
            ],
            'premium' => [
                ['name' => 'Alexander van Houten'],
                ['name' => 'Sebastiaan de Graaf'],
                ['name' => 'Martijn Wolters'],
                ['name' => 'Thomas van Beek'],
            ],
            'buurgarage' => [
                ['name' => 'Henk Bakker'],
                ['name' => 'Lars Visser'],
            ],
            default => [],
        };
    }

    private function createOwner(Company $company): void
    {
        // Get owner name based on company
        $ownerName = match($company->subdomain) {
            'piet' => 'Piet van der Berg',
            'snelle' => 'Mark Jansen',
            'premium' => 'Lisa de Vries',
            'buurgarage' => 'Jan Bakker',
            default => 'Eigenaar ' . $company->name,
        };

        // Create owner user account
        $ownerEmail = match($company->subdomain) {
            'piet' => 'piet@autogaragepiet.nl',
            'snelle' => 'mark@desnellegarage.nl',
            'premium' => 'lisa@premiummotors.nl',
            'buurgarage' => 'jan@buurgaragejan.nl',
            default => 'eigenaar@' . $company->subdomain . '.nl',
        };

        $ownerUser = User::firstOrCreate(
            ['email' => $ownerEmail],
            [
                'name' => $ownerName,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'role' => 'eigenaar', // ✅ FIXED
                'active' => true,
            ]
        );

        // Create owner employee record
        Employee::firstOrCreate(
            [
                'company_id' => $company->id,
                'user_id' => $ownerUser->id
            ],
            [
                'name' => $ownerName,
                'email' => $ownerEmail,
                'phone' => '06' . rand(10000000, 99999999),
                'position' => 'eigenaar',
                'role' => 'eigenaar', // ✅ FIXED
                'active' => true,
            ]
        );
    }

    private function createMedewerker(Company $company, array $template): void
    {
        // Create user account for medewerker
        $email = strtolower(str_replace(' ', '.', $template['name'])) . '@' . $company->subdomain . '.nl';
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $template['name'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'company_id' => $company->id,
                'role' => 'medewerker', // ✅ FIXED
                'active' => true,
            ]
        );

        // Create employee record
        Employee::firstOrCreate(
            [
                'company_id' => $company->id,
                'user_id' => $user->id
            ],
            [
                'name' => $template['name'],
                'email' => $email,
                'phone' => '06' . rand(10000000, 99999999),
                'position' => 'medewerker',
                'role' => 'medewerker', // ✅ FIXED
                'active' => true,
            ]
        );
    }
}
