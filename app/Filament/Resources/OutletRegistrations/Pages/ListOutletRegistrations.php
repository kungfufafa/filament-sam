<?php

namespace App\Filament\Resources\OutletRegistrations\Pages;

use App\Filament\Exports\OutletRegistrationExporter;
use App\Filament\Resources\OutletRegistrations\OutletRegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOutletRegistrations extends ListRecords
{
    protected static string $resource = OutletRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Export Pengajuan')
                ->exporter(OutletRegistrationExporter::class)
                ->modifyQueryUsing(fn (Builder $query): Builder => $this->getTableQueryForExport())
                ->authorize(fn (): bool => auth()->user()?->can('Export:OutletRegistration') ?? false),
            CreateAction::make(),
        ];
    }
}
