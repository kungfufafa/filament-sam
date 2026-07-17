<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OutletRegistrationStatus: string implements HasColor, HasLabel
{
    case Pending = 'PENDING';
    case Confirmed = 'CONFIRMED';
    case Approved = 'APPROVED';
    case Rejected = 'REJECTED';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
