<?php

namespace App\Filament\Exports;

use App\Enums\ScheduleScope;
use App\Models\Outlet;
use App\Models\PlanVisit;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PlanVisitExporter extends Exporter
{
    protected static ?string $model = PlanVisit::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('user.username')
                ->label('user_username'),
            ExportColumn::make('target_type')
                ->state(fn (PlanVisit $record): string => $record->visitable instanceof Outlet ? 'outlet' : 'outlet_registration'),
            ExportColumn::make('target_reference')
                ->state(fn (PlanVisit $record): ?string => $record->visitable?->code ?? $record->visitable?->name),
            ExportColumn::make('scheduled_at'),
            ExportColumn::make('schedule_scope')
                ->formatStateUsing(fn (ScheduleScope|string|null $state): ?string => $state instanceof ScheduleScope ? $state->value : $state),
            ExportColumn::make('period_start'),
            ExportColumn::make('period_end'),
            ExportColumn::make('schedule_week'),
            ExportColumn::make('schedule_year'),
            ExportColumn::make('realized_at'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['user', 'visitable']);
    }

    public static function getCompletedNotificationTitle(Export $export): string
    {
        return 'Export rencana kunjungan siap diunduh';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = Str::of('baris')->counted($export->successful_rows).' rencana kunjungan berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Str::of('baris')->counted($failedRowsCount).' gagal diekspor.';
        }

        return $body;
    }
}
