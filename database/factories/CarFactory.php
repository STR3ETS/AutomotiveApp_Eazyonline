<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Car;
use App\Models\Company;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    protected $model = Car::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'license_plate' => fake()->regexify('[A-Z]{2}-[0-9]{3}-[A-Z]{2}'),
            'brand' => fake()->randomElement(['BMW', 'Audi', 'Mercedes-Benz', 'Volkswagen', 'Ford']),
            'model' => fake()->words(2, true),
            'year' => fake()->numberBetween(2010, 2024),
            'mileage' => fake()->numberBetween(0, 300000),
            'price' => fake()->numberBetween(5000, 100000), // Regular price in euros
            'status' => fake()->randomElement(['available', 'sold', 'reserved']),
            'stage_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
