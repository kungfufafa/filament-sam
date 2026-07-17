<?php

namespace App\Filament\Resources\Outlets\Tables;

use App\Enums\OutletStatus;
use App\Filament\Exports\OutletExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class OutletsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Outlet')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('owner_name')
                    ->label('Pemilik')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->label('Telepon')
                    ->searchable(),
                TextColumn::make('businessEntity.name')
                    ->label('Badan Usaha')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('division.name')
                    ->label('Divisi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('geotags_count')
                    ->label('Geotag')
                    ->counts('geotags')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(OutletStatus::class),
                SelectFilter::make('business_entity_id')
                    ->label('Badan Usaha')
                    ->relationship('businessEntity', 'name'),
                SelectFilter::make('division_id')
                    ->label('Divisi')
                    ->relationship('division', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(OutletExporter::class)
                        ->authorize(fn (): bool => auth()->user()?->can('Export:Outlet') ?? false),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
