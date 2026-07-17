<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_edit_profile_form_with_custom_fields(): void
    {
        Filament::setCurrentPanel('admin');

        $user = User::factory()->create([
            'username' => 'sales_test',
            'name' => 'Sales Test',
            'email' => 'sales_test@example.com',
            'whatsapp_number' => '08123456789',
        ]);

        $this->actingAs($user);

        Livewire::test(EditProfile::class)
            ->assertFormSet([
                'username' => 'sales_test',
                'name' => 'Sales Test',
                'email' => 'sales_test@example.com',
                'whatsapp_number' => '08123456789',
            ]);
    }

    public function test_authenticated_user_can_update_profile_information(): void
    {
        Filament::setCurrentPanel('admin');

        $user = User::factory()->create([
            'username' => 'sales_test',
            'name' => 'Sales Test',
            'email' => 'sales_test@example.com',
            'whatsapp_number' => '08123456789',
        ]);

        $this->actingAs($user);

        Livewire::test(EditProfile::class)
            ->fillForm([
                'username' => 'sales_updated',
                'name' => 'Sales Updated',
                'email' => 'sales_updated@example.com',
                'whatsapp_number' => '08987654321',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'sales_updated',
            'name' => 'Sales Updated',
            'email' => 'sales_updated@example.com',
            'whatsapp_number' => '08987654321',
        ]);
    }

    public function test_change_password_button_is_visible_to_authorized_users(): void
    {
        Filament::setCurrentPanel('admin');

        $user = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'View:ChangePassword', 'guard_name' => 'web']);
        $user->givePermissionTo($permission);

        // Clear Spatie Permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user);

        Livewire::test(EditProfile::class)
            ->assertActionVisible('changePassword');
    }

    public function test_change_password_button_is_hidden_from_unauthorized_users(): void
    {
        Filament::setCurrentPanel('admin');

        $user = User::factory()->create();

        // Clear Spatie Permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user);

        Livewire::test(EditProfile::class)
            ->assertActionHidden('changePassword');
    }
}
