<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Seed companies with their data (this will also create car stages)
        $this->call(CompanySeeder::class);
        
        // Seed employees for all companies
        $this->call(EmployeeSeeder::class);
        
        // Seed listing templates for all companies
        $this->call(ListingTemplateSeeder::class);
        
        // Seed car images (must run after CompanySeeder as it creates cars)
        $this->call(CarImageSeeder::class);
        
        // Legacy seeders (commented out as they don't support multi-tenancy yet)
        // $this->call(\Database\Seeders\CarStagesTableSeeder::class);
        // $this->call(\Database\Seeders\CarsTableSeeder::class);
        // $this->call(\Database\Seeders\ChecklistSeeder::class);
    }
}
