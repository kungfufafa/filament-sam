<?php

namespace App\Filament\Resources\Visits\Tables;

use App\Filament\Exports\VisitExporter;
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

class VisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurred_at')
                    ->label('Tanggal')
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
                TextColumn::make('purpose')
                    ->label('Tipe Visit')
                    ->searchable(),
                TextColumn::make('transaction_status')
                    ->label('Transaksi')
                    ->badge(),
                TextColumn::make('duration_minutes')
                    ->label('Durasi (m)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
                        ->exporter(VisitExporter::class)
                        ->authorize(fn (): bool => auth()->user()?->can('Export:Visit') ?? false),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
