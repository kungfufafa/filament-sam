<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\ChangePassword;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ChangePasswordPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_access_change_password_page(): void
    {
        Filament::setCurrentPanel('admin');

        $user = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'View:ChangePassword', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        // Clear Spatie Permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user);

        Livewire::test(ChangePassword::class)
            ->assertStatus(200);
    }

    public function test_user_without_permission_cannot_access_change_password_page(): void
    {
        Filament::setCurrentPanel('admin');

        $user = User::factory()->create();

        // Clear Spatie Permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user);

        Livewire::test(ChangePassword::class)
            ->assertStatus(403);
    }
}
