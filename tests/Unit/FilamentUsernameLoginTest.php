<?php

namespace Tests\Unit;

use App\Filament\Pages\Auth\Login;
use Filament\Schemas\Components\Component;
use PHPUnit\Framework\TestCase;

class FilamentUsernameLoginTest extends TestCase
{
    public function test_login_uses_username_or_email_credentials(): void
    {
        $login = new class extends Login
        {
            /**
             * @param  array<string, mixed>  $data
             * @return array<string, mixed>
             */
            public function credentials(array $data): array
            {
                return $this->getCredentialsFromFormData($data);
            }

            public function loginComponent(): Component
            {
                return $this->getEmailFormComponent();
            }
        };

        $this->assertSame('login', $login->loginComponent()->getName());
        $this->assertSame([
            'username' => 'sales01',
            'password' => 'secret-password',
        ], $login->credentials([
            'login' => ' sales01 ',
            'password' => 'secret-password',
        ]));
        $this->assertSame([
            'email' => 'sales@example.com',
            'password' => 'secret-password',
        ], $login->credentials([
            'login' => 'sales@example.com',
            'password' => 'secret-password',
        ]));
    }
}
