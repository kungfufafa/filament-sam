<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    protected static bool $shouldPreventFormulaInjection = true;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('username')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('whatsapp_number')
                ->rules(['nullable', 'max:20']),
            ImportColumn::make('password')
                ->sensitive()
                ->ignoreBlankState()
                ->rules(['nullable', 'min:8', 'max:255']),
            ImportColumn::make('manager_username')
                ->relationship('tm', resolveUsing: 'username'),
            ImportColumn::make('role_names')
                ->relationship('roles', resolveUsing: 'name')
                ->multiple('|'),
            ImportColumn::make('business_entity_codes')
                ->relationship('businessEntities', resolveUsing: 'code')
                ->multiple('|'),
            ImportColumn::make('division_codes')
                ->relationship('divisions', resolveUsing: 'code')
                ->multiple('|'),
            ImportColumn::make('region_codes')
                ->relationship('regions', resolveUsing: 'code')
                ->multiple('|'),
            ImportColumn::make('cluster_codes')
                ->relationship('clusters', resolveUsing: 'code')
                ->multiple('|'),
        ];
    }

    public function resolveRecord(): User
    {
        return User::withTrashed()->firstOrNew([
            'username' => $this->data['username'],
        ]);
    }

    protected function beforeSave(): void
    {
        if (! $this->record->exists && blank($this->record->password)) {
            throw new RowImportFailedException('Password wajib diisi untuk pengguna baru.');
        }

        if ($this->record->trashed()) {
            $this->record->restore();
        }
    }

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return 'Import pengguna selesai';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = Number::format($import->successful_rows).' baris pengguna berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' baris gagal diimpor.';
        }

        return $body;
    }
}
