<?php

namespace Database\Factories;

use App\Models\BusinessEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessEntity>
 */
class BusinessEntityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->lexify('BU-????'),
            'name' => fake()->unique()->company(),
        ];
    }
}
