<?php

namespace Database\Seeders;

use App\Models\Bunga;
use App\Models\RekomendasiStok;
use Illuminate\Database\Seeder;

class RekomendasiStokSeeder extends Seeder
{
    public function run(): void
    {
        $bungas = Bunga::select('id_bunga', 'jumlah_terjual', 'harga', 'kualitas', 'popularitas', 'keuntungan')->get();

        if ($bungas->isEmpty()) {
            // Jika belum ada data bunga, keluar saja.
            return;
        }

        // Matrix data
        $matrix = [];
        foreach ($bungas as $b) {
            $matrix[$b->id_bunga] = [
                'jumlah_terjual' => (float) ($b->jumlah_terjual ?? 0),
                'harga' => (float) ($b->harga ?? 0),
                'kualitas' => (float) ($b->kualitas ?? 0),
                'popularitas' => (float) ($b->popularitas ?? 0),
                'keuntungan' => (float) ($b->keuntungan ?? 0),
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
    }
}