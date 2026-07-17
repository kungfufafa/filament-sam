<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OneSignalSubscriptionMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_one_signal_subscription_table_tracks_each_device_subscription(): void
    {
        $this->assertTrue(Schema::hasColumns('one_signal_subscriptions', [
            'user_id',
            'subscription_id',
            'platform',
            'created_at',
            'updated_at',
        ]));
    }
}
