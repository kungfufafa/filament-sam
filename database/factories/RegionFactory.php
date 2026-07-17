<?php

namespace Database\Factories;

use App\Models\BusinessEntity;
use App\Models\Division;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Region>
 */
class RegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->lexify('REG-????'),
            'business_entity_id' => BusinessEntity::factory(),
            'division_id' => Division::factory(),
            'name' => fake()->city(),
        ];
    }
}
