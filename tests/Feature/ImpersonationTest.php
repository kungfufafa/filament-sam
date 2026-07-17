<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_impersonate(): void
    {
        $user = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'ImpersonateUser', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $this->assertTrue($user->canImpersonate());
    }

    public function test_user_without_permission_cannot_impersonate(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->canImpersonate());
    }

    public function test_super_admin_cannot_be_impersonated(): void
    {
        $superAdmin = User::factory()->create();
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->assignRole('super_admin');

        $this->assertFalse($superAdmin->canBeImpersonated());
    }

    public function test_regular_user_can_be_impersonated(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->canBeImpersonated());
    }
}
