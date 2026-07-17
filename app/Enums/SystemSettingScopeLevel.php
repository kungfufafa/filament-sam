<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SystemSettingScopeLevel: string implements HasLabel
{
    case Global = 'global';
    case BusinessEntity = 'business_entity';
    case Division = 'division';
    case Region = 'region';
    case Cluster = 'cluster';

    public function getLabel(): string
    {
        return match ($this) {
            self::Global => 'Global',
            self::BusinessEntity => 'Badan Usaha',
            self::Division => 'Division',
            self::Region => 'Region',
            self::Cluster => 'Cluster',
        };
    }
}
