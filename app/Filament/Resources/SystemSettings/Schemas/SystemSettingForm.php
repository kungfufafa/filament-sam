<?php

namespace App\Filament\Resources\SystemSettings\Schemas;

use App\Enums\SystemSettingScopeLevel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SystemSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cakupan Pengaturan (Hierarchy Scoping)')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('scope_level')
                                ->label('Level Scope Pengaturan')
                                ->options(SystemSettingScopeLevel::class)
                                ->default(SystemSettingScopeLevel::Global->value)
                                ->required()
                                ->columnSpanFull(),
                            Select::make('business_entity_id')
                                ->label('Badan Usaha')
                                ->relationship('businessEntity', 'name')
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                            Select::make('division_id')
                                ->label('Divisi')
                                ->relationship('division', 'name')
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                            Select::make('region_id')
                                ->label('Region')
                                ->relationship('region', 'name')
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                            Select::make('cluster_id')
                                ->label('Cluster')
                                ->relationship('cluster', 'name')
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make('Parameter Operasional')
                    ->schema([
                        Grid::make(3)->schema([
                            Toggle::make('allow_outlet_registration_visits')
                                ->label('Izinkan Visit ke Calon Outlet (Register / NOO)')
                                ->default(true)
                                ->columnSpan(1),
                            TextInput::make('default_outlet_registration_radius')
                                ->label('Default Radius Toleransi (meter)')
                                ->numeric()
                                ->default(100)
                                ->required()
                                ->columnSpan(1),
                            TextInput::make('plan_visit_min_days')
                                ->label('Min. Hari Pengajuan Plan Visit')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->columnSpan(1),
                        ]),
                    ]),
            ]);
    }
}
