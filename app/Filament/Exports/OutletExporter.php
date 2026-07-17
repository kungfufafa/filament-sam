<?php

namespace App\Filament\Exports;

use App\Enums\OutletStatus;
use App\Models\Outlet;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;

class OutletExporter extends Exporter
{
    protected static ?string $model = Outlet::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('code'),
            ExportColumn::make('name'),
            ExportColumn::make('owner_name'),
            ExportColumn::make('phone_number'),
            ExportColumn::make('businessEntity.code')
                ->label('business_entity_code'),
            ExportColumn::make('division.code')
                ->label('division_code'),
            ExportColumn::make('region.code')
                ->label('region_code'),
            ExportColumn::make('cluster.code')
                ->label('cluster_code'),
            ExportColumn::make('geotags')
                ->label('geotags')
                ->formatStateUsing(fn ($state): string => $state
                    ->map(fn ($geotag): string => implode('|', [
                        $geotag->name,
                        $geotag->district,
                        $geotag->address,
                        $geotag->coordinates,
                        $geotag->radius,
                        $geotag->is_primary ? 'primary' : 'secondary',
                        $geotag->is_active ? 'active' : 'inactive',
                        $geotag->shop_sign_photo,
                        $geotag->storefront_photo,
                        $geotag->left_side_photo,
                        $geotag->right_side_photo,
                        $geotag->video,
                    ]))
                    ->implode(';')),
            ExportColumn::make('limit'),
            ExportColumn::make('status')
                ->formatStateUsing(fn (OutletStatus|string|null $state): ?string => $state instanceof OutletStatus ? $state->value : $state),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationTitle(Export $export): string
    {
        return 'Export outlet siap diunduh';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = Str::of('baris')->counted($export->successful_rows).' outlet berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Str::of('baris')->counted($failedRowsCount).' gagal diekspor.';
        }

        return $body;
    }
}
