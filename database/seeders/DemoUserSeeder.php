<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('DemoUserSeeder hanya boleh dijalankan pada environment local atau testing.');
        }

        $role = Role::query()
            ->where('name', 'ADMIN')
            ->where('can_access_web', true)
            ->first()
            ?? Role::query()->where('can_access_web', true)->first();

        $attributes = [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
            'whatsapp_number' => '081234567890',
        ];

        if (Schema::hasColumn('users', 'email_verified_at')) {
            $attributes['email_verified_at'] = now();
        }

        if (Schema::hasColumn('users', 'whatsapp_verified_at')) {
            $attributes['whatsapp_verified_at'] = now();
        }

        $user = User::withTrashed()->updateOrCreate(
            ['username' => 'demo'],
            $attributes,
        );

        if ($user->trashed()) {
            $user->restore();
        }

        if ($role !== null) {
            $user->syncRoles([$role]);
        }
    }
}
