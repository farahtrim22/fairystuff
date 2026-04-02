<?php

namespace App\Filament\Widgets;

use App\Models\Bunga;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Database\Eloquent\Builder;

class TopSellingFlowers extends BaseTableWidget
{
    protected static ?string $heading = '5 Bunga Terlaris';

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Bunga::query()
            ->orderByDesc('jumlah_terjual')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('nama_bunga')
                ->label('Nama Bunga')
                ->sortable(),
            TextColumn::make('jumlah_terjual')
                ->label('Terjual')
                ->numeric()
                ->sortable(),
            TextColumn::make('stok')
                ->label('Stok')
                ->numeric()
                ->sortable(),
        ];
    }
}