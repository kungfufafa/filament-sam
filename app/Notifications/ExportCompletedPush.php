<?php

namespace App\Notifications;

use App\Notifications\Channels\OneSignalChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ExportCompletedPush extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $exportId,
        public readonly string $title,
        public readonly string $body,
        public readonly string $url,
    ) {}

    /**
     * @return array<int, class-string>
     */
    public function via(object $notifiable): array
    {
        if (
            blank(config('services.one_signal.app_id'))
            || blank(config('services.one_signal.api_key'))
        ) {
            return [];
        }

        return [OneSignalChannel::class];
    }

    /**
     * @return array{
     *     headings: array{en: string},
     *     contents: array{en: string},
     *     data: array{type: string, export_id: int},
     *     url: string
     * }
     */
    public function toOneSignal(object $notifiable): array
    {
        return [
            'headings' => ['en' => $this->title],
            'contents' => ['en' => $this->body],
            'data' => [
                'type' => 'export_completed',
                'export_id' => $this->exportId,
            ],
            'url' => $this->url,
        ];
    }
}
