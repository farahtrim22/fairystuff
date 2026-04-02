<?php

namespace App\Filament\Resources\Bungas;

use App\Filament\Resources\Bungas\Pages\CreateBunga;
use App\Filament\Resources\Bungas\Pages\EditBunga;
use App\Filament\Resources\Bungas\Pages\ListBungas;
use App\Filament\Resources\Bungas\Schemas\BungaForm;
use App\Filament\Resources\Bungas\Tables\BungasTable;
use App\Models\Bunga;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BungaResource extends Resource
{
    protected static ?string $model = Bunga::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Data Bunga';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return BungaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BungasTable::configure($table);
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
            'index' => ListBungas::route('/'),
            'create' => CreateBunga::route('/create'),
            'edit' => EditBunga::route('/{record}/edit'),
        ];
    }
}
