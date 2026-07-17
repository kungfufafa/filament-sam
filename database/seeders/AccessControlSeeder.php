<?php

namespace Database\Seeders;

use App\Enums\OrganizationalScopeLevel;
use App\Models\Permission;
use App\Models\Role;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class AccessControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::query()
            ->where('name', 'like', 'Replicate:%')
            ->orWhere('name', 'like', 'Reorder:%')
            ->delete();

        $permissions = collect(FilamentShield::getAllResourcePermissionsWithLabels())
            ->keys()
            ->filter()
            ->unique()
            ->map(fn (string $name): Permission => Permission::findOrCreate($name, 'web'));

        $superAdmin = Role::findOrCreate(config('filament-shield.super_admin.name'), 'web');
        $superAdmin->forceFill([
            'organizational_scope_level' => OrganizationalScopeLevel::All,
            'can_access_web' => true,
            'can_access_mobile' => true,
        ])->save();
        $superAdmin->syncPermissions($permissions);

        $admin = Role::query()->firstOrCreate(
            ['name' => 'ADMIN', 'guard_name' => 'web'],
            [
                'organizational_scope_level' => OrganizationalScopeLevel::All,
                'can_access_web' => true,
                'can_access_mobile' => true,
            ],
        );
        $admin->syncPermissions($permissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
