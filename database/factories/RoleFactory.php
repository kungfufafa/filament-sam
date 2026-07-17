<?php

namespace Database\Factories;

use App\Enums\OrganizationalScopeLevel;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'guard_name' => 'web',
            'can_access_web' => true,
            'can_access_mobile' => true,
            'organizational_scope_level' => OrganizationalScopeLevel::Cluster->value,
        ];
    }
}
