<?php

namespace Tests\Feature;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Users\UserResource;
use App\Models\Role;
use App\Models\User;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Database\Seeders\AccessControlSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RoleResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_page_can_render_without_soft_delete_support(): void
    {
        $this->seed(AccessControlSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(Role::query()->where('name', 'ADMIN')->firstOrFail());

        $this->actingAs($user);
        Filament::setCurrentPanel('admin');

        Livewire::test(ListRoles::class)
            ->assertOk();
    }

    public function test_create_role_page_uses_filament_shield_permission_fields(): void
    {
        $this->seed(AccessControlSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(Role::query()->where('name', 'ADMIN')->firstOrFail());

        $this->actingAs($user);
        Filament::setCurrentPanel('admin');

        Livewire::test(CreateRole::class)
            ->assertOk()
            ->assertFormFieldExists('select_all')
            ->assertFormFieldExists(UserResource::class);

        $permissionNames = collect(FilamentShield::getAllResourcePermissionsWithLabels())->keys();

        $this->assertFalse($permissionNames->contains(fn (string $permission): bool => str_starts_with($permission, 'Replicate:')));
        $this->assertFalse($permissionNames->contains(fn (string $permission): bool => str_starts_with($permission, 'Reorder:')));
    }

    public function test_create_role_page_saves_filament_shield_permissions(): void
    {
        $this->seed(AccessControlSeeder::class);

        $user = User::factory()->create();
        $user->assignRole(Role::query()->where('name', 'ADMIN')->firstOrFail());

        $this->actingAs($user);
        Filament::setCurrentPanel('admin');

        $permission = array_key_first(FilamentShield::getResourcePermissionsWithLabels(UserResource::class));

        $this->assertNotNull($permission);

        Livewire::test(CreateRole::class)
            ->fillForm([
                'name' => 'SALES_MANAGER',
                'guard_name' => 'web',
                UserResource::class => [$permission],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertTrue(
            Role::query()->where('name', 'SALES_MANAGER')->firstOrFail()->hasPermissionTo($permission),
        );
    }
}
