<?php

namespace App\Filament\Resources\RekomendasiStoks\Pages;

use App\Filament\Resources\RekomendasiStoks\RekomendasiStokResource;
use App\Models\Bunga;
use App\Models\RekomendasiStok;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListRekomendasiStoks extends ListRecords
{
    protected static string $resource = RekomendasiStokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('hitungRekomendasiStok')
                ->label('Hitung Rekomendasi Stok')
                ->color('primary')
                ->action(function () {
                    $bungas = Bunga::select('id_bunga', 'jumlah_terjual', 'harga', 'kualitas', 'popularitas', 'keuntungan')->get();

                    if ($bungas->isEmpty()) {
                        Notification::make()
                            ->title('Tidak ada data bunga')
                            ->warning()
                            ->send();
                        return;
                    }

                    // Matrix data
                    $matrix = [];
                    foreach ($bungas as $b) {
                        $matrix[$b->id_bunga] = [
                            'jumlah_terjual' => (float) $b->jumlah_terjual,
                            'harga' => (float) $b->harga,
                            'kualitas' => (float) $b->kualitas,
                            'popularitas' => (float) $b->popularitas,
                            'keuntungan' => (float) $b->keuntungan,
                        ];
                    }

                    // Normalisasi (vector normalization)
                    $criteriaKeys = ['jumlah_terjual','harga','kualitas','popularitas','keuntungan'];
                    $denom = [];
                    foreach ($criteriaKeys as $key) {
                        $sumSquares = 0.0;
                        foreach ($matrix as $row) {
                            $sumSquares += pow($row[$key], 2);
                        }
                        $denom[$key] = $sumSquares > 0 ? sqrt($sumSquares) : 1.0; // hindari div-by-zero
                    }

                    // Bobot
                    $weights = [
                        'jumlah_terjual' => 0.30, // Benefit
                        'harga' => 0.20,           // Cost
                        'kualitas' => 0.20,        // Benefit
                        'popularitas' => 0.15,     // Benefit
                        'keuntungan' => 0.15,      // Benefit
                    ];

                    // Weighted normalized decision matrix
                    $weighted = [];
                    foreach ($matrix as $idBunga => $row) {
                        $weighted[$idBunga] = [];
                        foreach ($criteriaKeys as $key) {
                            $norm = $row[$key] / $denom[$key];
                            $weighted[$idBunga][$key] = $norm * $weights[$key];
                        }
                    }

                    // Ideal positif/negatif
                    $idealPos = [];
                    $idealNeg = [];
                    foreach ($criteriaKeys as $key) {
                        $values = array_column($weighted, $key);
                        if ($key === 'harga') { // cost
                            $idealPos[$key] = min($values);
                            $idealNeg[$key] = max($values);
                        } else { // benefit
                            $idealPos[$key] = max($values);
                            $idealNeg[$key] = min($values);
                        }
                    }

                    // Jarak ke ideal
                    $results = [];
                    foreach ($weighted as $idBunga => $row) {
                        $dPos = 0.0; $dNeg = 0.0;
                        foreach ($criteriaKeys as $key) {
                            $dPos += pow($row[$key] - $idealPos[$key], 2);
                            $dNeg += pow($row[$key] - $idealNeg[$key], 2);
                        }
                        $dPos = sqrt($dPos); $dNeg = sqrt($dNeg);
                        $vi = ($dPos + $dNeg) > 0 ? $dNeg / ($dPos + $dNeg) : 0.0;
                        $results[$idBunga] = $vi;
                    }

                    // Ranking desc by Vi
                    arsort($results); // high to low
                    $ranked = [];
                    $rank = 1;
                    foreach ($results as $idBunga => $vi) {
                        $ranked[] = [
                            'id_bunga' => $idBunga,
                            'vi' => round($vi, 4),
                            'rank' => $rank++,
                        ];
                    }

                    // Status rekomendasi
                    $total = count($ranked);
                    $mid = (int) ceil($total / 2);

                    // Simpan
                    RekomendasiStok::truncate();
                    foreach ($ranked as $row) {
                        $status = $row['rank'] === 1
                            ? 'Direkomendasikan'
                            : ($row['rank'] <= $mid ? 'Cadangan' : 'Tidak Direkomendasikan');

                        RekomendasiStok::updateOrCreate(
                            ['id_bunga' => $row['id_bunga']],
                            [
                                'nilai_preferensi' => $row['vi'],
                                'ranking' => $row['rank'],
                                'status_rekomendasi' => $status,
                            ]
                        );
                    }

                    Notification::make()
                        ->title('Perhitungan rekomendasi stok berhasil dilakukan menggunakan metode TOPSIS.')
                        ->success()
                        ->send();

                    // Segarkan tabel agar hasil terbaru tampil
                    $this->resetTable();
                }),
            // Tambah tombol Cetak Laporan PDF
            Action::make('cetakLaporanPdf')
                ->label('Cetak Laporan PDF')
                ->icon('heroicon-o-printer')
                ->color('secondary')
                ->action(function () {
                    $data = RekomendasiStok::with('bunga')->orderBy('ranking')->get();

                    if ($data->isEmpty()) {
                        Notification::make()
                            ->title('Tidak ada data untuk dicetak')
                            ->warning()
                            ->send();
                        return;
                    }

                    // Redirect ke route download agar browser dapat mengunduh file
                    return $this->redirect(route('rekomendasi-stok.cetak.pdf'));
                }),
        ];
    }
}