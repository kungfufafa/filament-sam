<?php

namespace Tests\Feature;

use App\Models\OneSignalSubscription;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OneSignalSubscriptionEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_filament_panel_initializes_one_signal(): void
    {
        config()->set('services.one_signal.app_id', fake()->uuid());
        Filament::setCurrentPanel('admin');

        $this->actingAs(User::factory()->create());

        $this->view('filament.one-signal')
            ->assertSee('one-signal-filament-config')
            ->assertSee('OneSignalSDK.page.js');
    }

    public function test_filament_login_page_does_not_initialize_identified_one_signal_user(): void
    {
        config()->set('services.one_signal.app_id', fake()->uuid());
        Filament::setCurrentPanel('admin');

        $this->view('filament.one-signal')
            ->assertDontSee('one-signal-filament-config');
    }

    public function test_authenticated_panel_does_not_load_one_signal_when_app_id_is_empty(): void
    {
        config()->set('services.one_signal.app_id');
        Filament::setCurrentPanel('admin');

        $this->actingAs(User::factory()->create());

        $this->view('filament.one-signal')
            ->assertDontSee('one-signal-filament-config')
            ->assertDontSee('OneSignalSDK.page.js');
    }

    public function test_authenticated_user_can_store_web_subscription(): void
    {
        $user = User::factory()->create();
        $subscriptionId = fake()->uuid();

        $this->actingAs($user)
            ->postJson(route('one-signal.subscriptions.store'), [
                'subscription_id' => $subscriptionId,
            ])
            ->assertNoContent();

        $subscription = OneSignalSubscription::query()
            ->where('subscription_id', $subscriptionId)
            ->firstOrFail();

        $this->assertTrue($subscription->user->is($user));
        $this->assertSame('web', $subscription->platform);
    }

    public function test_subscription_is_reassigned_when_another_user_logs_in_on_same_browser(): void
    {
        $previousUser = User::factory()->create();
        $currentUser = User::factory()->create();
        $subscription = OneSignalSubscription::factory()->for($previousUser)->create([
            'platform' => 'web',
        ]);

        $this->actingAs($currentUser)
            ->postJson(route('one-signal.subscriptions.store'), [
                'subscription_id' => $subscription->subscription_id,
            ])
            ->assertNoContent();

        $this->assertTrue($subscription->refresh()->user->is($currentUser));
    }

    public function test_authenticated_user_can_remove_own_subscription_on_logout(): void
    {
        $user = User::factory()->create();
        $subscription = OneSignalSubscription::factory()->for($user)->create([
            'platform' => 'web',
        ]);

        $this->actingAs($user)
            ->deleteJson(route('one-signal.subscriptions.destroy'), [
                'subscription_id' => $subscription->subscription_id,
            ])
            ->assertNoContent();

        $this->assertModelMissing($subscription);
    }

    public function test_user_cannot_remove_another_users_subscription(): void
    {
        $subscriptionOwner = User::factory()->create();
        $otherUser = User::factory()->create();
        $subscription = OneSignalSubscription::factory()->for($subscriptionOwner)->create([
            'platform' => 'web',
        ]);

        $this->actingAs($otherUser)
            ->deleteJson(route('one-signal.subscriptions.destroy'), [
                'subscription_id' => $subscription->subscription_id,
            ])
            ->assertNoContent();

        $this->assertModelExists($subscription);
    }
}
