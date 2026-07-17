<?php

namespace App\Filament\Imports;

use App\Enums\ScheduleScope;
use App\Models\Outlet;
use App\Models\OutletRegistration;
use App\Models\PlanVisit;
use App\Models\User;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Illuminate\Validation\Rule;

class PlanVisitImporter extends Importer
{
    protected static ?string $model = PlanVisit::class;

    protected static bool $shouldPreventFormulaInjection = true;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('user_username')
                ->requiredMapping()
                ->fillRecordUsing(fn (): null => null)
                ->rules(['required', 'max:255']),
            ImportColumn::make('target_type')
                ->requiredMapping()
                ->fillRecordUsing(fn (): null => null)
                ->rules(['required', Rule::in(['outlet', 'outlet_registration'])]),
            ImportColumn::make('target_reference')
                ->requiredMapping()
                ->fillRecordUsing(fn (): null => null)
                ->rules(['required', 'max:255']),
            ImportColumn::make('scheduled_at')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('schedule_scope')
                ->requiredMapping()
                ->rules(['required', Rule::enum(ScheduleScope::class)]),
            ImportColumn::make('period_start')
                ->rules(['nullable', 'date']),
            ImportColumn::make('period_end')
                ->rules(['nullable', 'date', 'after_or_equal:period_start']),
            ImportColumn::make('schedule_week')
                ->integer()
                ->rules(['nullable', 'integer', 'between:1,53']),
            ImportColumn::make('schedule_year')
                ->integer()
                ->rules(['nullable', 'integer', 'between:2000,2100']),
        ];
    }

    public function resolveRecord(): PlanVisit
    {
        $user = User::query()
            ->where('username', $this->data['user_username'])
            ->first();

        if ($user === null) {
            throw new RowImportFailedException("Pengguna [{$this->data['user_username']}] tidak ditemukan.");
        }

        $target = $this->resolveTarget();

        return PlanVisit::withTrashed()->firstOrNew([
            'user_id' => $user->getKey(),
            'visitable_type' => $target::class,
            'visitable_id' => $target->getKey(),
            'scheduled_at' => $this->data['scheduled_at'],
        ]);
    }

    protected function beforeSave(): void
    {
        if ($this->record->trashed()) {
            $this->record->restore();
        }
    }

    private function resolveTarget(): Model
    {
        $model = match ($this->data['target_type']) {
            'outlet' => Outlet::class,
            'outlet_registration' => OutletRegistration::class,
            default => throw new RowImportFailedException('Tipe target tidak valid.'),
        };

        $target = $model::query()
            ->where('code', $this->data['target_reference'])
            ->orWhere('name', $this->data['target_reference'])
            ->first();

        if ($target === null) {
            throw new RowImportFailedException("Target [{$this->data['target_reference']}] tidak ditemukan.");
        }

        return $target;
    }

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return 'Import rencana kunjungan selesai';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = Number::format($import->successful_rows).' rencana kunjungan berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' baris gagal diimpor.';
        }

        return $body;
    }
}
