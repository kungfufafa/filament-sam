<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->schema([
                        Grid::make(2)->schema([
                            FileUpload::make('profile_photo_path')
                                ->label('Foto Profil')
                                ->avatar()
                                ->imageEditor()
                                ->directory('users/profile-photos')
                                ->visibility('public')
                                ->columnSpanFull(),
                            TextInput::make('username')
                                ->label('Username')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('name')
                                ->label('Nama Lengkap / Pengguna')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('whatsapp_number')
                                ->label('Nomor WhatsApp')
                                ->required()
                                ->maxLength(20)
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make('Peran & Atasan Langsung')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('roles')
                                ->label('Role Jabatan')
                                ->relationship('roles', 'name')
                                ->multiple()
                                ->required()
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                            Select::make('tm_id')
                                ->label('Atasan Langsung (Territory Manager / Supervisor)')
                                ->relationship('tm', 'name')
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make('Cakupan Wilayah / Anchor (Pivot Scoping)')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('businessEntities')
                                ->label('Badan Usaha (Pivot Anchor)')
                                ->relationship('businessEntities', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->columnSpan(1),
                            Select::make('divisions')
                                ->label('Divisi (Pivot Anchor)')
                                ->relationship('divisions', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->columnSpan(1),
                            Select::make('regions')
                                ->label('Region (Pivot Anchor)')
                                ->relationship('regions', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->columnSpan(1),
                            Select::make('clusters')
                                ->label('Cluster (Pivot Anchor)')
                                ->relationship('clusters', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->columnSpan(1),
                        ]),
                    ]),
            ]);
    }
}
