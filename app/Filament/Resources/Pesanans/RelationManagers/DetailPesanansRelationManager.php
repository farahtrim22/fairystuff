<?php

namespace App\Filament\Resources\Pesanans\RelationManagers;

use App\Models\Bucket;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailPesanansRelationManager extends RelationManager
{
    protected static string $relationship = 'detail';
    protected static ?string $title = 'Detail Pesanan';

    public function form(Schema $schema): Schema
    {
        // Hanya tampilan; tidak digunakan karena action create/edit/delete dinonaktifkan
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bucket.nama_bucket')
                    ->label('Nama Produk')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('idr', true)
                    ->sortable(),
            ])
            ->headerActions([])
            ->recordActions([])
            ->bulkActions([]);
    }
}