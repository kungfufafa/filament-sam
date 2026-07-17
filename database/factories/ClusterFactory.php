<?php

namespace Database\Factories;

use App\Models\BusinessEntity;
use App\Models\Cluster;
use App\Models\Division;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cluster>
 */
class ClusterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->lexify('CLS-????'),
            'business_entity_id' => BusinessEntity::factory(),
            'division_id' => Division::factory(),
            'region_id' => Region::factory(),
            'name' => fake()->city(),
        ];
    }
}
