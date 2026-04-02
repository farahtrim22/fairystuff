<?php

namespace App\Filament\Resources\DetailPesanans\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetailPesanansTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('pesanan.nama_pelanggan')
                ->label('Pelanggan')
                ->searchable(),
            TextColumn::make('bucket.nama_bucket')
                ->label('Bucket')
                ->searchable(),
            TextColumn::make('jumlah')
                ->label('Jumlah')
                ->numeric()
                ->sortable(),
            TextColumn::make('subtotal')
                ->label('Subtotal')
                ->money('idr', true)
                ->sortable(),
        ]);
    }
}