<?php

namespace App\Filament\Exports;

use App\Models\Visit;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;

class VisitExporter extends Exporter
{
    protected static ?string $model = Visit::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user.name'),
            ExportColumn::make('visitable_type'),
            ExportColumn::make('visitable_id'),
            ExportColumn::make('occurred_at'),
            ExportColumn::make('purpose'),
            ExportColumn::make('check_in_coordinates'),
            ExportColumn::make('check_out_coordinates'),
            ExportColumn::make('check_in_time'),
            ExportColumn::make('check_out_time'),
            ExportColumn::make('report'),
            ExportColumn::make('transaction_status'),
            ExportColumn::make('duration_minutes'),
            ExportColumn::make('check_in_photo'),
            ExportColumn::make('check_out_photo'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your visit export has completed and '.Str::of('row')->counted($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Str::of('row')->counted($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
