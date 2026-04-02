<?php

namespace App\Filament\Widgets;

use App\Models\Bunga;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalBunga = Bunga::query()->count();
        $totalCustomer = Schema::hasTable('customers') ? DB::table('customers')->count() : 0;
        $totalTransaksi = Schema::hasTable('transaksis') ? DB::table('transaksis')->count() : 0;
        $bungaHabis = Schema::hasColumn('bungas', 'stok') ? Bunga::query()->where('stok', 0)->count() : 0;

        return [
            Stat::make('Total Jenis Bunga', (string) $totalBunga)
                ->icon('heroicon-o-sparkles')
                ->color('success'),

            Stat::make('Total Customer', (string) $totalCustomer)
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Total Transaksi', (string) $totalTransaksi)
                ->icon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make('Bunga Habis', (string) $bungaHabis)
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning'),
        ];
    }
}