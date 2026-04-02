<?php

namespace App\Filament\Resources\Pesanans\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PesanansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable(),

                TextColumn::make('tanggal_pesan')
                    ->label('Tanggal')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('status_pesanan')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'Menunggu',
                        'info' => 'Diproses',
                        'success' => 'Selesai',
                        'danger' => 'Dibatalkan',
                    ]),

                TextColumn::make('detail_count')
                    ->label('Jumlah Item')
                    ->counts('detail')
                    ->sortable(),

                TextColumn::make('total_sum')
                    ->label('Total (subtotal)')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('total_harga')
                    ->label('Total Harga (manual)')
                    ->money('idr', true)
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}