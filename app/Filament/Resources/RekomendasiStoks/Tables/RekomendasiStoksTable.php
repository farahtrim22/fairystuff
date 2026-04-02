<?php

namespace App\Filament\Resources\RekomendasiStoks\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RekomendasiStoksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bunga.nama_bunga')
                    ->label('Nama Bunga')
                    ->searchable(),
                TextColumn::make('nilai_preferensi')
                    ->label('Nilai Preferensi')
                    ->numeric(decimalPlaces: 4)
                    ->sortable(),
                TextColumn::make('ranking')
                    ->label('Ranking')
                    ->sortable(),
                TextColumn::make('status_rekomendasi')
                    ->label('Status Rekomendasi')
                    ->badge()
                    ->colors([
                        'success' => 'Direkomendasikan',
                        'warning' => 'Cadangan',
                        'danger' => 'Tidak Direkomendasikan',
                    ])
                    ->sortable(),
            ]);
    }
}