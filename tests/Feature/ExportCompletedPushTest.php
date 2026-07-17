<?php

namespace Tests\Feature;

use App\Filament\Exports\UserExporter;
use App\Models\User;
use App\Notifications\Channels\OneSignalChannel;
use App\Notifications\ExportCompletedPush;
use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ExportCompletedPushTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_completion_queues_push_for_exporting_user(): void
    {
        Notification::fake();
        config()->set('services.one_signal.app_id', '11111111-1111-4111-8111-111111111111');
        config()->set('services.one_signal.api_key', 'test-api-key');

        $user = User::factory()->create();
        $export = Export::query()->create([
            'user_id' => $user->getKey(),
            'file_disk' => 'local',
            'file_name' => 'users.csv',
            'exporter' => UserExporter::class,
            'processed_rows' => 10,
            'total_rows' => 10,
            'successful_rows' => 10,
        ]);

        UserExporter::modifyCompletedNotification(FilamentNotification::make(), $export);

        Notification::assertSentTo(
            $user,
            ExportCompletedPush::class,
            fn (ExportCompletedPush $notification): bool => $notification->exportId === $export->getKey()
                && $notification->title === 'Export pengguna siap diunduh',
        );
    }

    public function test_one_signal_channel_targets_all_subscriptions_using_external_id(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'api.onesignal.com/notifications' => Http::response(['id' => fake()->uuid()]),
        ]);

        config()->set('services.one_signal.app_id', '11111111-1111-4111-8111-111111111111');
        config()->set('services.one_signal.api_key', 'test-api-key');

        $user = User::factory()->create();
        $notification = new ExportCompletedPush(
            exportId: 123,
            title: 'Export selesai',
            body: 'File siap diunduh.',
            url: 'https://example.test/admin',
        );

        app(OneSignalChannel::class)->send($user, $notification);

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.onesignal.com/notifications'
            && $request->hasHeader('Authorization', 'Key test-api-key')
            && $request['include_aliases']['external_id'] === ["user:{$user->getKey()}"]
            && $request['target_channel'] === 'push'
            && $request['data']['export_id'] === 123);
    }

    public function test_one_signal_notification_is_disabled_when_credentials_are_empty(): void
    {
        Http::preventStrayRequests();
        config()->set('services.one_signal.app_id');
        config()->set('services.one_signal.api_key');

        $user = User::factory()->create();
        $notification = new ExportCompletedPush(
            exportId: 123,
            title: 'Export selesai',
            body: 'File siap diunduh.',
            url: 'https://example.test/admin',
        );

        $this->assertSame([], $notification->via($user));

        app(OneSignalChannel::class)->send($user, $notification);

        Http::assertNothingSent();
    }
}
