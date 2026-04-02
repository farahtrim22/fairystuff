<?php

namespace App\Filament\Resources\DetailPesanans;

use App\Filament\Resources\DetailPesanans\Pages;
use App\Filament\Resources\DetailPesanans\Schemas\DetailPesananForm;
use App\Filament\Resources\DetailPesanans\Tables\DetailPesanansTable;
use App\Models\DetailPesanan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailPesananResource extends Resource
{
    protected static ?string $model = DetailPesanan::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationLabel = 'Detail Pesanan';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return DetailPesananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DetailPesanansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDetailPesanans::route('/'),
            'create' => Pages\CreateDetailPesanan::route('/create'),
            'edit' => Pages\EditDetailPesanan::route('/{record}/edit'),
        ];
    }
}