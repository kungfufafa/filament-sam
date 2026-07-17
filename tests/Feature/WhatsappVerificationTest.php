<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_user_factory_has_email_and_whatsapp_verification_timestamps(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->email_verified_at);
        $this->assertNotNull($user->whatsapp_verified_at);
    }

    public function test_whatsapp_can_be_marked_as_unverified_independently(): void
    {
        $user = User::factory()->unverifiedWhatsapp()->create();

        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->whatsapp_verified_at);
    }
}
