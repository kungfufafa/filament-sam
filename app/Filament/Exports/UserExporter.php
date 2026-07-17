<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('username'),
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('whatsapp_number'),
            ExportColumn::make('tm.username')
                ->label('manager_username'),
            ExportColumn::make('role_names')
                ->state(fn (User $record): string => $record->roles->pluck('name')->implode('|')),
            ExportColumn::make('business_entity_codes')
                ->state(fn (User $record): string => $record->businessEntities->pluck('code')->implode('|')),
            ExportColumn::make('division_codes')
                ->state(fn (User $record): string => $record->divisions->pluck('code')->implode('|')),
            ExportColumn::make('region_codes')
                ->state(fn (User $record): string => $record->regions->pluck('code')->implode('|')),
            ExportColumn::make('cluster_codes')
                ->state(fn (User $record): string => $record->clusters->pluck('code')->implode('|')),
            ExportColumn::make('created_at'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with([
            'tm',
            'roles',
            'businessEntities',
            'divisions',
            'regions',
            'clusters',
        ]);
    }

    public static function getCompletedNotificationTitle(Export $export): string
    {
        return 'Export pengguna siap diunduh';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = Str::of('baris')->counted($export->successful_rows).' pengguna berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Str::of('baris')->counted($failedRowsCount).' gagal diekspor.';
        }

        return $body;
    }
}
