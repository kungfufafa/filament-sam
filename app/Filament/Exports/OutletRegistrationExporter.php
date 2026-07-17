<?php

namespace App\Filament\Exports;

use App\Models\OutletRegistration;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;

class OutletRegistrationExporter extends Exporter
{
    protected static ?string $model = OutletRegistration::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('businessEntity.name'),
            ExportColumn::make('division.name'),
            ExportColumn::make('region.name'),
            ExportColumn::make('cluster.name'),
            ExportColumn::make('tm.name'),
            ExportColumn::make('createdBy.name'),
            ExportColumn::make('code'),
            ExportColumn::make('type'),
            ExportColumn::make('name'),
            ExportColumn::make('address'),
            ExportColumn::make('owner_name'),
            ExportColumn::make('phone_number'),
            ExportColumn::make('representative_phone_number'),
            ExportColumn::make('owner_identity_number'),
            ExportColumn::make('district'),
            ExportColumn::make('shop_sign_photo'),
            ExportColumn::make('storefront_photo'),
            ExportColumn::make('left_side_photo'),
            ExportColumn::make('right_side_photo'),
            ExportColumn::make('owner_identity_photo'),
            ExportColumn::make('video'),
            ExportColumn::make('oppo'),
            ExportColumn::make('vivo'),
            ExportColumn::make('realme'),
            ExportColumn::make('samsung'),
            ExportColumn::make('xiaomi'),
            ExportColumn::make('fl'),
            ExportColumn::make('coordinates'),
            ExportColumn::make('limit'),
            ExportColumn::make('status'),
            ExportColumn::make('rejected_at'),
            ExportColumn::make('rejectedBy.name'),
            ExportColumn::make('confirmed_at'),
            ExportColumn::make('confirmedBy.name'),
            ExportColumn::make('approved_at'),
            ExportColumn::make('approvedBy.name'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your outlet registration export has completed and '.Str::of('row')->counted($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Str::of('row')->counted($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
