<?php

namespace App\Filament\Resources\Outlets\Schemas;

use App\Enums\OutletStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class OutletForm
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
                Section::make('Informasi Outlet & Lokasi Geospasial')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Kode Outlet')
                                ->unique(ignoreRecord: true)
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
                            Select::make('status')
                                ->label('Status Outlet')
                                ->options(OutletStatus::class)
                                ->default(OutletStatus::Maintain->value)
                                ->required()
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make('Geotag Outlet')
                    ->description('Tambahkan satu atau beberapa lokasi yang sah untuk check-in outlet.')
                    ->schema([
                        Repeater::make('geotags')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lokasi')
                                    ->placeholder('Contoh: Toko Utama atau Gudang')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('district')
                                    ->label('Distrik / Kecamatan')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('address')
                                    ->label('Alamat Lengkap')
                                    ->required()
                                    ->rows(2),
                                TextInput::make('coordinates')
                                    ->label('Koordinat (Lat, Long)')
                                    ->placeholder('-6.200000, 106.816666')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('radius')
                                    ->label('Radius Check-in (meter)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(100)
                                    ->required(),
                                Toggle::make('is_primary')
                                    ->label('Geotag Utama')
                                    ->default(false),
                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                                FileUpload::make('shop_sign_photo')
                                    ->label('Foto Shop Sign')
                                    ->image()
                                    ->directory('outlets/shop_signs')
                                    ->visibility('public'),
                                FileUpload::make('storefront_photo')
                                    ->label('Foto Depan')
                                    ->image()
                                    ->directory('outlets/depan')
                                    ->visibility('public'),
                                FileUpload::make('left_side_photo')
                                    ->label('Foto Sisi Kiri')
                                    ->image()
                                    ->directory('outlets/kiri')
                                    ->visibility('public'),
                                FileUpload::make('right_side_photo')
                                    ->label('Foto Sisi Kanan')
                                    ->image()
                                    ->directory('outlets/kanan')
                                    ->visibility('public'),
                                FileUpload::make('video')
                                    ->label('Video Outlet')
                                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                                    ->directory('outlets/videos')
                                    ->visibility('public'),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Geotag')
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),
                Section::make('Konfigurasi Pengamanan & Limit')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('limit')
                                ->label('Credit Limit')
                                ->numeric()
                                ->columnSpan(1),
                            Select::make('outlet_registration_id')
                                ->label('Sourced NOO Register')
                                ->relationship('outletRegistration', 'name')
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make('Dokumen Pemilik')
                    ->schema([
                        Grid::make(3)->schema([
                            FileUpload::make('owner_identity_photo')
                                ->label('Foto KTP')
                                ->image()
                                ->directory('outlets/ktp')
                                ->visibility('public')
                                ->columnSpan(1),
                        ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
