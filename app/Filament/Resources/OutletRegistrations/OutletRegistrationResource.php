<?php

namespace App\Filament\Resources\OutletRegistrations;

use App\Filament\Resources\Concerns\HasOrganizationalDataScope;
use App\Filament\Resources\OutletRegistrations\Pages\CreateOutletRegistration;
use App\Filament\Resources\OutletRegistrations\Pages\EditOutletRegistration;
use App\Filament\Resources\OutletRegistrations\Pages\ListOutletRegistrations;
use App\Filament\Resources\OutletRegistrations\Schemas\OutletRegistrationForm;
use App\Filament\Resources\OutletRegistrations\Tables\OutletRegistrationsTable;
use App\Models\OutletRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OutletRegistrationResource extends Resource
{
    use HasOrganizationalDataScope;

    protected static ?string $model = OutletRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return OutletRegistrationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OutletRegistrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOutletRegistrations::route('/'),
            'create' => CreateOutletRegistration::route('/create'),
            'edit' => EditOutletRegistration::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
