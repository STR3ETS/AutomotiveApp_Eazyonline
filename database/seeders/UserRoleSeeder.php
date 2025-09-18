<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->createUsersForCompany($company);
        }

        echo "✅ Users and roles created for all companies!\n";
    }

    private function createUsersForCompany(Company $company): void
    {
        // Create owner user (eigenaar)
        $owner = User::create([
            'name' => $this->getOwnerName($company),
            'email' => $this->getOwnerEmail($company),
            'password' => Hash::make('password123'),
            'company_id' => $company->id,
            'role' => 'eigenaar', // ✅ FIXED
            'active' => true,
        ]);

        // Koppel owner aan een employee-record (indien nog niet gekoppeld)
        $ownerEmployee = $company->employees()->where('position', 'eigenaar')->first();
        if ($ownerEmployee) {
            $ownerEmployee->update([
                'user_id' => $owner->id,
                'role' => 'eigenaar', // ✅ FIXED
            ]);
        }

        // Maak wat medewerkers met login
        $employees = $company->employees()
            ->where('position', 'medewerker')
            ->take(2)
            ->get();

        foreach ($employees as $employee) {
            $user = User::create([
                'name' => $employee->name,
                'email' => $this->getEmployeeEmail($employee),
                'password' => Hash::make('password123'),
                'company_id' => $company->id,
                'role' => 'medewerker', // ✅ FIXED
                'active' => true,
            ]);

            $employee->update([
                'user_id' => $user->id,
                'role' => 'medewerker', // ✅ FIXED
            ]);
        }

        echo "   Created users for {$company->name}\n";
    }

    private function getOwnerName(Company $company): string
    {
        return match($company->subdomain) {
            'piet' => 'Piet van der Garage',
            'snelle' => 'Mark Jansen',
            'premium' => 'Lisa de Vries',
            'buurgarage' => 'Jan Bakker',
            default => 'Eigenaar',
        };
    }

    private function getOwnerEmail(Company $company): string
    {
        return match($company->subdomain) {
            'piet' => 'piet@autogaragepiet.nl',
            'snelle' => 'mark@desnellegarage.nl',
            'premium' => 'lisa@premiummotors.nl',
            'buurgarage' => 'jan@buurgaragejan.nl',
            default => 'eigenaar@' . $company->subdomain . '.nl',
        };
    }

    private function getEmployeeEmail(Employee $employee): string
    {
        $cleanName = strtolower(str_replace(' ', '.', $employee->name));
        return $cleanName . '@' . $employee->company->subdomain . '.nl';
    }
}
