<?php

namespace App\Filament\Widgets;

use App\Models\RekomendasiStok;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Database\Eloquent\Builder;

class TopRecommendations extends BaseTableWidget
{
    protected static ?string $heading = 'Top 3 Rekomendasi Stok';

    // Buat widget mengambil lebar penuh pada dashboard
    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return RekomendasiStok::query()
            ->with('bunga')
            ->orderByDesc('nilai_preferensi')
            ->limit(3);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('bunga.nama_bunga')
                ->label('Nama Bunga')
                ->sortable(),
            TextColumn::make('nilai_preferensi')
                ->label('Nilai Preferensi')
                ->numeric(decimalPlaces: 4)
                ->sortable(),
            TextColumn::make('ranking')
                ->label('Ranking')
                ->sortable(),
            TextColumn::make('status_rekomendasi')
                ->label('Status')
                ->badge()
                ->colors([
                    'success' => 'Direkomendasikan',
                    'warning' => 'Cadangan',
                    'danger' => 'Tidak Direkomendasi',
                ]),
        ];
    }
}