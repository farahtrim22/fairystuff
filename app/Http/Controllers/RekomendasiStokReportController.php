<?php

namespace App\Http\Controllers;

use App\Models\RekomendasiStok;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class RekomendasiStokReportController extends Controller
{
    public function download(): Response
    {
        $data = RekomendasiStok::with('bunga')->orderBy('ranking')->get();

        if ($data->isEmpty()) {
            // Tidak ada data; kembalikan halaman sederhana agar jelas
            $html = '<html><body style="font-family: Arial, sans-serif; padding: 24px;">'
                . '<h2>Tidak ada data untuk dicetak</h2>'
                . '<p>Silakan hitung rekomendasi terlebih dahulu.</p>'
                . '</body></html>';
            return response($html, 200);
        }

        $generatedAt = now()->format('d F Y, H:i');
        $pdf = Pdf::loadView('pdf.laporan_rekomendasi', [
            'items' => $data,
            'generatedAt' => $generatedAt,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('laporan_rekomendasi_stok.pdf');
    }
}