<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class HorizonGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_access_horizon(): void
    {
        $user = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'ViewHorizon', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        $this->assertTrue(Gate::forUser($user)->check('viewHorizon'));
    }

    public function test_user_without_permission_cannot_access_horizon(): void
    {
        $user = User::factory()->create();

        $this->assertFalse(Gate::forUser($user)->check('viewHorizon'));
    }
}
