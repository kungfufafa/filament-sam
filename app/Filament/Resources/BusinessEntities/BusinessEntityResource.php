<?php

namespace App\Filament\Resources\BusinessEntities;

use App\Filament\Resources\BusinessEntities\Pages\ListBusinessEntities;
use App\Filament\Resources\BusinessEntities\Schemas\BusinessEntityForm;
use App\Filament\Resources\BusinessEntities\Tables\BusinessEntitiesTable;
use App\Filament\Resources\Concerns\HasOrganizationalDataScope;
use App\Models\BusinessEntity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BusinessEntityResource extends Resource
{
    use HasOrganizationalDataScope;

    protected static ?string $model = BusinessEntity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return BusinessEntityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusinessEntitiesTable::configure($table);
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
            'index' => ListBusinessEntities::route('/'),
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
