<?php

namespace App\Filament\Resources\Concerns;

use App\Models\User;
use App\Support\OrganizationalDataScope;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

trait HasOrganizationalDataScope
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Filament::auth()->user();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        return app(OrganizationalDataScope::class)->apply($query, $user);
    }
}
