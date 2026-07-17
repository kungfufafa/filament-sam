<?php

namespace App\Filament\Exports;

use App\Models\User;
use App\Notifications\ExportCompletedPush;
use Filament\Actions\Exports\Exporter as BaseExporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Notification;

abstract class Exporter extends BaseExporter
{
    public static function modifyCompletedNotification(Notification $notification, Export $export): Notification
    {
        if ($export->user instanceof User) {
            $export->user->notify(
                (new ExportCompletedPush(
                    exportId: $export->getKey(),
                    title: static::getCompletedNotificationTitle($export),
                    body: static::getCompletedNotificationBody($export),
                    url: route('filament.admin.pages.dashboard'),
                ))->afterCommit(),
            );
        }

        return parent::modifyCompletedNotification($notification, $export);
    }
}
