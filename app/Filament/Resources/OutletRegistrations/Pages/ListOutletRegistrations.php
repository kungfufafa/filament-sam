<?php

namespace App\Filament\Resources\OutletRegistrations\Pages;

use App\Filament\Resources\OutletRegistrations\OutletRegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOutletRegistrations extends ListRecords
{
    protected static string $resource = OutletRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
