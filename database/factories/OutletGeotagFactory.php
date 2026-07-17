<?php

namespace Database\Factories;

use App\Models\Outlet;
use App\Models\OutletGeotag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OutletGeotag>
 */
class OutletGeotagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'outlet_id' => Outlet::factory(),
            'name' => fake()->randomElement(['Utama', 'Pintu Samping', 'Gudang']),
            'district' => fake()->citySuffix(),
            'address' => fake()->address(),
            'coordinates' => fake()->latitude().', '.fake()->longitude(),
            'radius' => 100,
            'is_primary' => false,
            'is_active' => true,
        ];
    }
}
