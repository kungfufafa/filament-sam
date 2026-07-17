<?php

namespace Database\Factories;

use App\Models\BusinessEntity;
use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Division>
 */
class DivisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->lexify('DIV-????'),
            'business_entity_id' => BusinessEntity::factory(),
            'name' => fake()->word(),
        ];
    }
}
