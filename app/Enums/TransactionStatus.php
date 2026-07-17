<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TransactionStatus: string implements HasColor, HasLabel
{
    case Yes = 'YES';
    case No = 'NO';

    public function getLabel(): string
    {
        return match ($this) {
            self::Yes => 'Yes (Ada Transaksi)',
            self::No => 'No (Tidak Ada Transaksi)',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Yes => 'success',
            self::No => 'gray',
        };
    }
}
