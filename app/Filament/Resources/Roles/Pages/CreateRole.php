<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Override;

class CreateRole extends CreateRecord
{
    public Collection $permissions;

    protected static string $resource = RoleResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $modelFields = [
            'name',
            'guard_name',
            'parent_role_id',
            'organizational_scope_level',
            'can_access_web',
            'can_access_mobile',
        ];

        $this->permissions = collect($data)
            ->except([...$modelFields, 'select_all', Utils::getTenantModelForeignKey()])
            ->values()
            ->flatten()
            ->unique();

        if (Utils::isTenancyEnabled() && filled(Arr::get($data, Utils::getTenantModelForeignKey()))) {
            $modelFields[] = Utils::getTenantModelForeignKey();
        }

        return Arr::only($data, $modelFields);
    }

    protected function afterCreate(): void
    {
        $permissionModels = $this->permissions->map(fn (string $permission) => Utils::getPermissionModel()::firstOrCreate([
            'name' => $permission,
            'guard_name' => $this->data['guard_name'],
        ]));

        $this->record->syncPermissions($permissionModels);
    }
}
