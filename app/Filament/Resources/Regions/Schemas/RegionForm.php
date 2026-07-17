<?php

namespace App\Filament\Resources\Regions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class RegionForm
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
                    ->afterStateUpdated(fn (Set $set) => $set('division_id', null))
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
                    ->columnSpanFull(),
                TextInput::make('code')
                    ->label('Kode Region')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->label('Nama Region')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
