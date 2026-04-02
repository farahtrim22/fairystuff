<?php

namespace App\Filament\Widgets;

use App\Models\Bunga;
use App\Models\Pesanan;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class FairystuffStats extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        // Total penjualan bulan ini untuk pesanan yang selesai
        $totalPenjualanBulanIni = (float) Pesanan::query()
            ->where('status_pesanan', 'selesai')
            ->whereMonth('tanggal_pesan', now()->month)
            ->whereYear('tanggal_pesan', now()->year)
            ->sum('total_harga');

        // Jumlah pesanan dengan status diproses dan selesai
        $jumlahDiproses = (int) Pesanan::query()->where('status_pesanan', 'diproses')->count();
        $jumlahSelesai = (int) Pesanan::query()->where('status_pesanan', 'selesai')->count();

        // Total stok bunga tersedia
        $totalStokBunga = (int) Bunga::query()->sum('stok');

        return [
            Stat::make('Penjualan Bulan Ini', 'Rp ' . number_format($totalPenjualanBulanIni, 2, ',', '.'))
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Pesanan Diproses', (string) $jumlahDiproses)
                ->icon('heroicon-o-cog-6-tooth')
                ->color('info'),

            Stat::make('Pesanan Selesai', (string) $jumlahSelesai)
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Total Stok Bunga', (string) $totalStokBunga)
                ->icon('heroicon-o-sparkles')
                ->color('primary'),
        ];
    }
}