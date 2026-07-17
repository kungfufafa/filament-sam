<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OutletStatus: string implements HasColor, HasLabel
{
    case Maintain = 'MAINTAIN';
    case Unmaintain = 'UNMAINTAIN';
    case Unproductive = 'UNPRODUCTIVE';

    public function getLabel(): string
    {
        return match ($this) {
            self::Maintain => 'Maintain',
            self::Unmaintain => 'Unmaintain',
            self::Unproductive => 'Unproductive',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Maintain => 'success',
            self::Unmaintain => 'warning',
            self::Unproductive => 'danger',
        };
    }
}
