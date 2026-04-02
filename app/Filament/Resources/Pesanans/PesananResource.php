<?php

namespace App\Filament\Resources\Pesanans;

use App\Filament\Resources\Pesanans\Pages;
use App\Filament\Resources\Pesanans\Schemas\PesananForm;
use App\Filament\Resources\Pesanans\Tables\PesanansTable;
use App\Filament\Resources\Pesanans\RelationManagers\DetailPesanansRelationManager;
use App\Filament\Resources\Pesanans\RelationManagers\PembayaranRelationManager;
use App\Models\Pesanan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Pesanan';

    public static function form(Schema $schema): Schema
    {
        return PesananForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PesanansTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('detail')
            ->withSum('detail as total_sum', 'subtotal');
    }

    public static function getRelations(): array
    {
        return [
            DetailPesanansRelationManager::class,
            PembayaranRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}