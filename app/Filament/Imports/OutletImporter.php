<?php

namespace App\Filament\Imports;

use App\Enums\OutletStatus;
use App\Models\Outlet;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Validation\Rule;

class OutletImporter extends Importer
{
    protected static ?string $model = Outlet::class;

    protected static bool $shouldPreventFormulaInjection = true;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('owner_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('phone_number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('geotag_address')
                ->requiredMapping()
                ->rules(['required'])
                ->fillRecordUsing(fn (): null => null),
            ImportColumn::make('geotag_district')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->fillRecordUsing(fn (): null => null),
            ImportColumn::make('business_entity_code')
                ->relationship('businessEntity', resolveUsing: 'code')
                ->requiredMapping(),
            ImportColumn::make('division_code')
                ->relationship('division', resolveUsing: 'code')
                ->requiredMapping(),
            ImportColumn::make('region_code')
                ->relationship('region', resolveUsing: 'code')
                ->requiredMapping(),
            ImportColumn::make('cluster_code')
                ->relationship('cluster', resolveUsing: 'code')
                ->requiredMapping(),
            ImportColumn::make('geotag_coordinates')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->fillRecordUsing(fn (): null => null),
            ImportColumn::make('geotag_radius')
                ->integer()
                ->rules(['nullable', 'integer', 'min:1'])
                ->fillRecordUsing(fn (): null => null),
            ImportColumn::make('limit')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required', Rule::enum(OutletStatus::class)]),
        ];
    }

    public function resolveRecord(): Outlet
    {
        if (filled($this->data['code'] ?? null)) {
            return Outlet::withTrashed()->firstOrNew([
                'code' => $this->data['code'],
            ]);
        }

        return new Outlet;
    }

    protected function beforeSave(): void
    {
        if ($this->record->trashed()) {
            $this->record->restore();
        }
    }

    protected function afterSave(): void
    {
        if (blank($this->data['geotag_coordinates'] ?? null)) {
            return;
        }

        $this->record->geotags()->updateOrCreate(
            ['is_primary' => true],
            [
                'name' => 'Utama',
                'district' => $this->data['geotag_district'],
                'address' => $this->data['geotag_address'],
                'coordinates' => $this->data['geotag_coordinates'],
                'radius' => $this->data['geotag_radius'] ?? 100,
                'is_active' => true,
            ],
        );
    }

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return 'Import outlet selesai';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = Number::format($import->successful_rows).' baris outlet berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' baris gagal diimpor.';
        }

        return $body;
    }
}
