<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrganizationalScopeLevel: string implements HasLabel
{
    case All = 'all';
    case BusinessEntity = 'business_entity';
    case Divisi = 'division';
    case Region = 'region';
    case Cluster = 'cluster';

    public function getLabel(): string
    {
        return match ($this) {
            self::All => 'All (Semua)',
            self::BusinessEntity => 'Badan Usaha',
            self::Divisi => 'Divisi',
            self::Region => 'Region',
            self::Cluster => 'Cluster',
        };
    }

    public function priority(): int
    {
        return match ($this) {
            self::All => 0,
            self::BusinessEntity => 1,
            self::Divisi => 2,
            self::Region => 3,
            self::Cluster => 4,
        };
    }
}
