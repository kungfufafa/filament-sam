<?php

namespace App\Filament\Resources\Clusters\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ClusterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('business_entity_id')
                    ->label('Badan Usaha')
                    ->relationship('businessEntity', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('division_id', null);
                        $set('region_id', null);
                    })
                    ->columnSpanFull(),
                Select::make('division_id')
                    ->label('Divisi')
                    ->relationship('division', 'name', fn (Builder $query, Get $get) => $query->when(
                        $get('business_entity_id'),
                        fn ($query, $businessEntityId) => $query->where('business_entity_id', $businessEntityId)
                    ))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('region_id', null))
                    ->columnSpanFull(),
                Select::make('region_id')
                    ->label('Region')
                    ->relationship('region', 'name', fn (Builder $query, Get $get) => $query->when(
                        $get('division_id'),
                        fn ($query, $divisionId) => $query->where('division_id', $divisionId)
                    ))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                TextInput::make('code')
                    ->label('Kode Cluster')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->label('Nama Cluster')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
