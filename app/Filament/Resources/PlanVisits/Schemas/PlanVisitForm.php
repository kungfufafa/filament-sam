<?php

namespace App\Filament\Resources\PlanVisits\Schemas;

use App\Enums\ScheduleScope;
use App\Models\Outlet;
use App\Models\OutletRegistration;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PlanVisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rencana Kunjungan')
                    ->schema([
                        Grid::make(2)->schema([
                            DateTimePicker::make('scheduled_at')
                                ->label('Rencana Tanggal & Waktu')
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
                            Select::make('schedule_scope')
                                ->label('Lingkup Penjadwalan')
                                ->options(ScheduleScope::class)
                                ->default(ScheduleScope::Daily->value)
                                ->required()
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make('Rentang Waktu & Periode (Weekly/Monthly)')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('period_start')
                                ->label('Periode Mulai')
                                ->columnSpan(1),
                            DatePicker::make('period_end')
                                ->label('Periode Selesai')
                                ->columnSpan(1),
                            TextInput::make('schedule_week')
                                ->label('Minggu Ke (Week Number)')
                                ->numeric()
                                ->columnSpan(1),
                            TextInput::make('schedule_year')
                                ->label('Tahun')
                                ->numeric()
                                ->default(date('Y'))
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make('Realisasi (Jika Sudah Dikunjungi)')
                    ->schema([
                        Grid::make(2)->schema([
                            DateTimePicker::make('realized_at')
                                ->label('Waktu Realisasi')
                                ->columnSpan(1),
                            Select::make('realized_visit_id')
                                ->label('Kaitkan ke Realisasi Visit')
                                ->relationship('realizedVisit', 'occurred_at')
                                ->searchable()
                                ->preload()
                                ->columnSpan(1),
                        ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
