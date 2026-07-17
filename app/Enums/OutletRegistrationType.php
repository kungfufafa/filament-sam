<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OutletRegistrationType: string implements HasColor, HasLabel
{
    case Noo = 'NOO';
    case Lead = 'LEAD';

    public function getLabel(): string
    {
        return match ($this) {
            self::Noo => 'NOO',
            self::Lead => 'Lead',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Noo => 'info',
            self::Lead => 'warning',
        };
    }
}
