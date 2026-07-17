<?php

namespace Database\Factories;

use App\Enums\OutletRegistrationType;
use App\Models\OutletRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OutletRegistration>
 */
class OutletRegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => OutletRegistrationType::Noo,
        ];
    }
}
