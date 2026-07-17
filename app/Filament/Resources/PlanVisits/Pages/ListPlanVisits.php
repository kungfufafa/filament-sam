<?php

namespace App\Filament\Resources\PlanVisits\Pages;

use App\Filament\Exports\PlanVisitExporter;
use App\Filament\Imports\PlanVisitImporter;
use App\Filament\Resources\PlanVisits\PlanVisitResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPlanVisits extends ListRecords
{
    protected static string $resource = PlanVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Import Plan Visit')
                ->importer(PlanVisitImporter::class)
                ->authorize(fn (): bool => auth()->user()?->can('Import:PlanVisit') ?? false),
            ExportAction::make()
                ->label('Export Plan Visit')
                ->exporter(PlanVisitExporter::class)
                ->modifyQueryUsing(fn (Builder $query): Builder => $this->getTableQueryForExport())
                ->authorize(fn (): bool => auth()->user()?->can('Export:PlanVisit') ?? false),
            CreateAction::make(),
        ];
    }
}
