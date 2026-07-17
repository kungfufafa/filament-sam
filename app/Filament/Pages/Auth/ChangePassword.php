<?php

namespace App\Filament\Pages\Auth;

use Apriansyahrs\MekayaTheme\Auth\MekayaChangePassword;

class ChangePassword extends MekayaChangePassword
{
    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:ChangePassword') ?? false;
    }
}
