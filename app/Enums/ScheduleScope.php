<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ScheduleScope: string implements HasLabel
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';

    public function getLabel(): string
    {
        return match ($this) {
            self::Daily => 'Harian (Daily)',
            self::Weekly => 'Mingguan (Weekly)',
            self::Monthly => 'Bulanan (Monthly)',
        };
    }
}
