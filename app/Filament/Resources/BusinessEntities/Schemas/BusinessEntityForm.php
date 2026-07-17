<?php

namespace App\Filament\Resources\BusinessEntities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BusinessEntityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Badan Usaha')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->label('Nama Badan Usaha')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
