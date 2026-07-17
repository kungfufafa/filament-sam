<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Enums\OrganizationalScopeLevel;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Role & Hirarki')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Role')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('guard_name')
                                ->label('Guard Name')
                                ->default('web')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),
                            Select::make('parent_role_id')
                                ->label('Parent Role (Self-Reference)')
                                ->relationship('parent', 'name')
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                            Select::make('organizational_scope_level')
                                ->label('Level Akses Data (Scope Level)')
                                ->options(OrganizationalScopeLevel::class)
                                ->default(OrganizationalScopeLevel::Cluster->value)
                                ->required()
                                ->columnSpan(1),
                        ]),
                        Grid::make(2)->schema([
                            Toggle::make('can_access_web')
                                ->label('Dapat Mengakses Web')
                                ->default(true)
                                ->columnSpan(1),
                            Toggle::make('can_access_mobile')
                                ->label('Dapat Mengakses Mobile')
                                ->default(true)
                                ->columnSpan(1),
                            RoleResource::getSelectAllFormComponent()
                                ->columnSpanFull(),
                        ]),
                    ]),
                RoleResource::getShieldFormComponents(),
            ]);
    }
}
