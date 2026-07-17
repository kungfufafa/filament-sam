<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_repeatable_demo_user(): void
    {
        $this->seed(DemoUserSeeder::class);
        $this->seed(DemoUserSeeder::class);

        $user = User::query()->where('username', 'demo')->sole();

        $this->assertSame('demo@example.com', $user->email);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertSame(1, User::query()->where('username', 'demo')->count());
    }
}
