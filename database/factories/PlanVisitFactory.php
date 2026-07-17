<?php

namespace Database\Factories;

use App\Enums\ScheduleScope;
use App\Models\PlanVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlanVisit>
 */
class PlanVisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'schedule_scope' => fake()->randomElement(ScheduleScope::cases()),
        ];
    }
}
