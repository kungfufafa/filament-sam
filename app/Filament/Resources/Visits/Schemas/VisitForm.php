<?php

namespace App\Filament\Resources\Visits\Schemas;

use App\Enums\TransactionStatus;
use App\Models\Outlet;
use App\Models\OutletRegistration;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kunjungan & Target')
                    ->schema([
                        Grid::make(2)->schema([
                            DateTimePicker::make('occurred_at')
                                ->label('Tanggal & Waktu Visit')
                                ->default(now())
                                ->required()
                                ->columnSpan(1),
                            Select::make('user_id')
                                ->label('Sales / Petugas Visit')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(1),
                            MorphToSelect::make('visitable')
                                ->label('Tujuan Visit (Outlet / NOO Register)')
                                ->types([
                                    MorphToSelect\Type::make(Outlet::class)->titleAttribute('name')->label('Outlet Aktif'),
                                    MorphToSelect\Type::make(OutletRegistration::class)->titleAttribute('name')->label('NOO Register (Calon Outlet)'),
                                ])
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpanFull(),
                            TextInput::make('purpose')
                                ->label('Tipe / Tujuan Visit')
                                ->placeholder('Rutin / Prospect / Follow Up')
                                ->maxLength(255)
                                ->columnSpan(1),
                            Select::make('transaction_status')
                                ->label('Ada Transaksi?')
                                ->options(TransactionStatus::class)
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make('Geospasial & Waktu Check-In / Out')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('check_in_coordinates')
                                ->label('Geotag Check-In')
                                ->placeholder('-6.200000, 106.816666')
                                ->columnSpan(1),
                            TextInput::make('check_out_coordinates')
                                ->label('Geotag Check-Out')
                                ->placeholder('-6.200000, 106.816666')
                                ->columnSpan(1),
                            TextInput::make('duration_minutes')
                                ->label('Durasi Visit (Menit)')
                                ->numeric()
                                ->columnSpan(1),
                        ]),
                        Grid::make(2)->schema([
                            DateTimePicker::make('check_in_time')
                                ->label('Waktu Check-In')
                                ->columnSpan(1),
                            DateTimePicker::make('check_out_time')
                                ->label('Waktu Check-Out')
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make('Laporan & Dokumentasi Foto')
                    ->schema([
                        Textarea::make('report')
                            ->label('Laporan / Hasil Kunjungan')
                            ->rows(4)
                            ->columnSpanFull(),
                        Grid::make(2)->schema([
                            FileUpload::make('check_in_photo')
                                ->label('Foto Check-In')
                                ->image()
                                ->directory('visits/in')
                                ->visibility('public')
                                ->columnSpan(1),
                            FileUpload::make('check_out_photo')
                                ->label('Foto Check-Out')
                                ->image()
                                ->directory('visits/out')
                                ->visibility('public')
                                ->columnSpan(1),
                        ]),
                    ]),
            ]);
    }
}
