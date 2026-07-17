<?php

namespace App\Filament\Resources\Outlets\Pages;

use App\Filament\Exports\OutletExporter;
use App\Filament\Imports\OutletImporter;
use App\Filament\Resources\Outlets\OutletResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOutlets extends ListRecords
{
    protected static string $resource = OutletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Import Outlet')
                ->importer(OutletImporter::class)
                ->authorize(fn (): bool => auth()->user()?->can('Import:Outlet') ?? false),
            ExportAction::make()
                ->label('Export Outlet')
                ->exporter(OutletExporter::class)
                ->modifyQueryUsing(fn (Builder $query): Builder => $this->getTableQueryForExport())
                ->authorize(fn (): bool => auth()->user()?->can('Export:Outlet') ?? false),
            CreateAction::make(),
        ];
    }
}
