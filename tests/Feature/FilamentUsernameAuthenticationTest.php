<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentUsernameAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_to_filament_with_username(): void
    {
        Filament::setCurrentPanel('admin');

        $user = User::factory()->create([
            'username' => 'sales01',
            'email' => 'sales@example.com',
            'password' => 'secret-password',
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'login' => 'sales01',
                'password' => 'secret-password',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_log_in_to_filament_with_email(): void
    {
        Filament::setCurrentPanel('admin');

        $user = User::factory()->create([
            'username' => 'sales01',
            'email' => 'sales@example.com',
            'password' => 'secret-password',
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'login' => 'sales@example.com',
                'password' => 'secret-password',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($user);
    }
}
