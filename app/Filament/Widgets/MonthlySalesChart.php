<?php

namespace App\Filament\Widgets;

use App\Models\Pesanan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlySalesChart extends ChartWidget
{
    protected ?string $heading = 'Penjualan per Bulan';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        // Ambil total_harga per bulan untuk tahun berjalan
        $year = now()->year;
        $rows = Pesanan::query()
            ->selectRaw('MONTH(tanggal_pesan) as bulan, COALESCE(SUM(total_harga), 0) as total')
            ->whereYear('tanggal_pesan', $year)
            ->where('status_pesanan', 'selesai')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $labels = [];
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create()->month($m)->locale('id')->monthName;
            $data[] = (float) ($rows[$m]->total ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Penjualan',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(249, 168, 212, 0.7)', // pink pastel
                    ],
                    'borderColor' => 'rgba(245, 208, 254, 0.9)', // lavender
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}