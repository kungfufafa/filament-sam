<?php

namespace App\Filament\Resources\SystemSettings\Tables;

use App\Enums\SystemSettingScopeLevel;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SystemSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scope_level')
                    ->label('Scope Level')
                    ->badge()
                    ->sortable(),
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
                TextColumn::make('region.name')
                    ->label('Region')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('cluster.name')
                    ->label('Cluster')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('allow_outlet_registration_visits')
                    ->label('NOO Visit Allowed')
                    ->boolean(),
                TextColumn::make('default_outlet_registration_radius')
                    ->label('Radius (m)')
                    ->numeric(),
                TextColumn::make('plan_visit_min_days')
                    ->label('Min Plan Days')
                    ->numeric(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('scope_level')
                    ->label('Scope Level')
                    ->options(SystemSettingScopeLevel::class),
                SelectFilter::make('business_entity_id')
                    ->label('Badan Usaha')
                    ->relationship('businessEntity', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
