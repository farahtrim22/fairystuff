<?php

namespace App\Filament\Resources\Pembayarans;

use App\Filament\Resources\Pembayarans\Pages;
use App\Filament\Resources\Pembayarans\Schemas\PembayaranForm;
use App\Filament\Resources\Pembayarans\Tables\PembayaransTable;
use App\Models\Pembayaran;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Pembayaran';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return PembayaranForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PembayaransTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
}
