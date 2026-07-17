<?php

namespace Database\Factories;

use App\Models\OneSignalSubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OneSignalSubscription>
 */
class OneSignalSubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subscription_id' => fake()->uuid(),
            'platform' => fake()->randomElement(['android', 'ios', 'web']),
        ];
    }
}
