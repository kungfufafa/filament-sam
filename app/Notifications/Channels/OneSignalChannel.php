<?php

namespace App\Notifications\Channels;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Throwable;

class OneSignalChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $appId = config('services.one_signal.app_id');
        $apiKey = config('services.one_signal.api_key');

        if (blank($appId) || blank($apiKey) || ! method_exists($notification, 'toOneSignal')) {
            return;
        }

        /** @var array<string, mixed> $message */
        $message = $notification->toOneSignal($notifiable);

        Http::baseUrl('https://api.onesignal.com')
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Authorization' => "Key {$apiKey}",
            ])
            ->connectTimeout(3)
            ->timeout(10)
            ->retry(
                [100, 500],
                fn (Throwable $exception, PendingRequest $request): bool => $exception instanceof ConnectionException
                    || ($exception instanceof RequestException && $exception->response->serverError()),
            )
            ->post('/notifications', [
                'app_id' => $appId,
                'include_aliases' => [
                    'external_id' => [$notifiable->routeNotificationForOneSignal()],
                ],
                'target_channel' => 'push',
                ...$message,
            ])
            ->throw();
    }
}
