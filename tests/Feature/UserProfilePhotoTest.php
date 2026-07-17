<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_profile_photo_path_can_be_stored(): void
    {
        $user = User::factory()->create([
            'profile_photo_path' => 'users/profile-photos/avatar.jpg',
        ]);

        $this->assertSame('users/profile-photos/avatar.jpg', $user->profile_photo_path);
        $this->assertSame(
            Storage::disk('public')->url('users/profile-photos/avatar.jpg'),
            $user->getFilamentAvatarUrl(),
        );
    }

    public function test_filament_uses_fallback_avatar_when_user_has_no_profile_photo(): void
    {
        $user = User::factory()->create([
            'profile_photo_path' => null,
        ]);

        $this->assertNull($user->getFilamentAvatarUrl());
    }
}
