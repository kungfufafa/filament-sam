<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\OutletRegistration;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class OutletRegistrationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OutletRegistration');
    }

    public function view(AuthUser $authUser, OutletRegistration $outletRegistration): bool
    {
        return $authUser->can('View:OutletRegistration');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OutletRegistration');
    }

    public function update(AuthUser $authUser, OutletRegistration $outletRegistration): bool
    {
        return $authUser->can('Update:OutletRegistration');
    }

    public function delete(AuthUser $authUser, OutletRegistration $outletRegistration): bool
    {
        return $authUser->can('Delete:OutletRegistration');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:OutletRegistration');
    }

    public function restore(AuthUser $authUser, OutletRegistration $outletRegistration): bool
    {
        return $authUser->can('Restore:OutletRegistration');
    }

    public function forceDelete(AuthUser $authUser, OutletRegistration $outletRegistration): bool
    {
        return $authUser->can('ForceDelete:OutletRegistration');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:OutletRegistration');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:OutletRegistration');
    }

    public function export(AuthUser $authUser, OutletRegistration $outletRegistration): bool
    {
        return $authUser->can('Export:OutletRegistration');
    }
}
