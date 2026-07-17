<?php

namespace App\Filament\Resources\OutletRegistrations\Schemas;

use App\Enums\OutletRegistrationStatus;
use App\Enums\OutletRegistrationType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class OutletRegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hirarki Organisasi')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('business_entity_id')
                                ->label('Badan Usaha')
                                ->relationship('businessEntity', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function (Set $set) {
                                    $set('division_id', null);
                                    $set('region_id', null);
                                    $set('cluster_id', null);
                                })
                                ->columnSpan(1),
                            Select::make('division_id')
                                ->label('Divisi')
                                ->relationship('division', 'name', fn (Builder $query, Get $get) => $query->when(
                                    $get('business_entity_id'),
                                    fn ($q, $id) => $q->where('business_entity_id', $id)
                                ))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function (Set $set) {
                                    $set('region_id', null);
                                    $set('cluster_id', null);
                                })
                                ->columnSpan(1),
                            Select::make('region_id')
                                ->label('Region')
                                ->relationship('region', 'name', fn (Builder $query, Get $get) => $query->when(
                                    $get('division_id'),
                                    fn ($q, $id) => $q->where('division_id', $id)
                                ))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('cluster_id', null))
                                ->columnSpan(1),
                            Select::make('cluster_id')
                                ->label('Cluster')
                                ->relationship('cluster', 'name', fn (Builder $query, Get $get) => $query->when(
                                    $get('region_id'),
                                    fn ($q, $id) => $q->where('region_id', $id)
                                ))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make('Data Identitas & Alamat Outlet')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Kode Outlet')
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('name')
                                ->label('Nama Outlet')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('owner_name')
                                ->label('Nama Pemilik')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('phone_number')
                                ->label('Nomor Telepon')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('representative_phone_number')
                                ->label('Nomor Wakil / PIC')
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('owner_identity_number')
                                ->label('Nomor KTP / NIK')
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('district')
                                ->label('Distrik / Kecamatan')
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('coordinates')
                                ->label('Sourced Geotag (Lat,Long)')
                                ->placeholder('-6.200000, 106.816666')
                                ->maxLength(255)
                                ->columnSpan(1),
                            Textarea::make('address')
                                ->label('Alamat Lengkap')
                                ->required()
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ]),
                Section::make('Limit & Status Persetujuan')
                    ->schema([
                        Grid::make(4)->schema([
                            Select::make('type')
                                ->label('Type')
                                ->options(OutletRegistrationType::class)
                                ->default(OutletRegistrationType::Noo->value)
                                ->required()
                                ->columnSpan(1),
                            TextInput::make('limit')
                                ->label('Credit Limit / Target')
                                ->numeric()
                                ->columnSpan(1),
                            Select::make('status')
                                ->label('Status Registrasi')
                                ->options(OutletRegistrationStatus::class)
                                ->default(OutletRegistrationStatus::Pending->value)
                                ->required()
                                ->columnSpan(1),
                            Select::make('tm_id')
                                ->label('Territory Manager (TM)')
                                ->relationship('tm', 'name')
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                        ]),
                        TextInput::make('notes')
                            ->label('Keterangan / Catatan')
                            ->columnSpanFull(),
                    ]),
                Section::make('Dokumentasi & Brand Kompetitor / Etalase')
                    ->schema([
                        Grid::make(3)->schema([
                            FileUpload::make('shop_sign_photo')
                                ->label('Foto Shop Sign / Plang')
                                ->image()
                                ->directory('outlet_registrations/shop_signs')
                                ->visibility('public')
                                ->columnSpan(1),
                            FileUpload::make('storefront_photo')
                                ->label('Foto Depan Outlet')
                                ->image()
                                ->directory('outlet_registrations/depan')
                                ->visibility('public')
                                ->columnSpan(1),
                            FileUpload::make('owner_identity_photo')
                                ->label('Foto KTP Pemilik')
                                ->image()
                                ->directory('outlet_registrations/ktp')
                                ->visibility('public')
                                ->columnSpan(1),
                            FileUpload::make('left_side_photo')
                                ->label('Foto Sisi Kiri')
                                ->image()
                                ->directory('outlet_registrations/kiri')
                                ->visibility('public')
                                ->columnSpan(1),
                            FileUpload::make('right_side_photo')
                                ->label('Foto Sisi Kanan')
                                ->image()
                                ->directory('outlet_registrations/kanan')
                                ->visibility('public')
                                ->columnSpan(1),
                            FileUpload::make('video')
                                ->label('Video Dokumentasi')
                                ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                                ->directory('outlet_registrations/videos')
                                ->visibility('public')
                                ->columnSpan(1),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('oppo')->label('Data / Penjualan OPPO')->columnSpan(1),
                            TextInput::make('vivo')->label('Data / Penjualan VIVO')->columnSpan(1),
                            TextInput::make('realme')->label('Data / Penjualan REALME')->columnSpan(1),
                            TextInput::make('samsung')->label('Data / Penjualan SAMSUNG')->columnSpan(1),
                            TextInput::make('xiaomi')->label('Data / Penjualan XIAOMI')->columnSpan(1),
                            TextInput::make('fl')->label('Frontliner (FL) info')->columnSpan(1),
                        ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
