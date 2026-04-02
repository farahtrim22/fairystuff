<?php

namespace App\Filament\Resources\RekomendasiStoks;

use App\Filament\Resources\RekomendasiStoks\Pages;
use App\Filament\Resources\RekomendasiStoks\Schemas\RekomendasiStokForm;
use App\Filament\Resources\RekomendasiStoks\Tables\RekomendasiStoksTable;
use App\Models\RekomendasiStok;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RekomendasiStokResource extends Resource
{
    protected static ?string $model = RekomendasiStok::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-light-bulb';
    protected static ?string $navigationLabel = 'Rekomendasi Stok';

    public static function form(Schema $schema): Schema
    {
        return RekomendasiStokForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RekomendasiStoksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRekomendasiStoks::route('/'),
        ];
    }
}