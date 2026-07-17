<?php

namespace App\Filament\Resources\PlanVisits\Tables;

use App\Enums\ScheduleScope;
use App\Filament\Exports\PlanVisitExporter;
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

class PlanVisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label('Rencana Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Sales / Petugas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('visitable_type')
                    ->label('Tipe Tujuan')
                    ->formatStateUsing(fn (string $state): string => str_contains($state, 'Outlet') ? 'Outlet Aktif' : 'NOO Register')
                    ->badge()
                    ->color(fn (string $state): string => str_contains($state, 'Outlet') ? 'success' : 'info')
                    ->sortable(),
                TextColumn::make('schedule_scope')
                    ->label('Lingkup')
                    ->badge()
                    ->sortable(),
                TextColumn::make('realized_at')
                    ->label('Status Realisasi')
                    ->formatStateUsing(fn ($state) => $state ? 'Terealisasi' : 'Belum')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('schedule_scope')
                    ->label('Lingkup')
                    ->options(ScheduleScope::class),
                SelectFilter::make('user_id')
                    ->label('Sales / Petugas')
                    ->relationship('user', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exporter(PlanVisitExporter::class)
                        ->authorize(fn (): bool => auth()->user()?->can('Export:PlanVisit') ?? false),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
