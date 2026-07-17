<?php

namespace App\Filament\Pages\Auth;

use Apriansyahrs\MekayaTheme\Auth\MekayaEditProfile;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EditProfile extends MekayaEditProfile
{
    /**
     * Configure the edit profile form.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('profile_photo_path')
                    ->label('Foto Profil')
                    ->avatar()
                    ->imageEditor()
                    ->directory('users/profile-photos')
                    ->visibility('public')
                    ->columnSpanFull(),

                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                $this->getNameFormComponent(),

                $this->getEmailFormComponent(),

                TextInput::make('whatsapp_number')
                    ->label('WhatsApp Number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changePassword')
                ->label(__('mekaya::ui.profile.actions.change_password'))
                ->url(ChangePassword::getUrl())
                ->visible(fn (): bool => auth()->user()?->can('View:ChangePassword') ?? false),
        ];
    }
}
