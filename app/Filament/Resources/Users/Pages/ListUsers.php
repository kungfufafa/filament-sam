<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Exports\UserExporter;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->label('Import Pengguna')
                ->importer(UserImporter::class)
                ->authorize(fn (): bool => auth()->user()?->can('Import:User') ?? false),
            ExportAction::make()
                ->label('Export Pengguna')
                ->exporter(UserExporter::class)
                ->modifyQueryUsing(fn (Builder $query): Builder => $this->getTableQueryForExport())
                ->authorize(fn (): bool => auth()->user()?->can('Export:User') ?? false),
            CreateAction::make(),
        ];
    }
}
