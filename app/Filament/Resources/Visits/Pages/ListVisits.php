<?php

namespace App\Filament\Resources\Visits\Pages;

use App\Filament\Exports\VisitExporter;
use App\Filament\Resources\Visits\VisitResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListVisits extends ListRecords
{
    protected static string $resource = VisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label('Export Kunjungan')
                ->exporter(VisitExporter::class)
                ->modifyQueryUsing(fn (Builder $query): Builder => $this->getTableQueryForExport())
                ->authorize(fn (): bool => auth()->user()?->can('Export:Visit') ?? false),
            CreateAction::make(),
        ];
    }
}
