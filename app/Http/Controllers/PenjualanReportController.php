<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PenjualanReportController extends Controller
{
    public function download(): Response
    {
        // Ambil pesanan beserta relasi pembayaran dan detail
        $pesanans = Pesanan::with(['pembayaran', 'detail'])
            ->orderBy('tanggal_pesan', 'asc')
            ->get();

        if ($pesanans->isEmpty()) {
            $html = '<html><body style="font-family: Arial, sans-serif; padding: 24px;">'
                . '<h2>Tidak ada data penjualan untuk dicetak</h2>'
                . '<p>Silakan tambahkan pesanan terlebih dahulu.</p>'
                . '</body></html>';
            return response($html, 200);
        }

        // Susun data ringkas untuk tabel
        $rows = [];
        foreach ($pesanans as $p) {
            $totalItem = (int) $p->detail->sum('jumlah');
            $totalHarga = (float) $p->detail->sum('subtotal');
            $statusBayar = $p->pembayaran->status_pembayaran ?? 'belum dibayar';

            $rows[] = [
                'tanggal' => (string) ($p->tanggal_pesan ?? ''),
                'pelanggan' => (string) ($p->nama_pelanggan ?? ''),
                'total_item' => $totalItem,
                'total_harga' => $totalHarga,
                'status_pembayaran' => (string) $statusBayar,
            ];
        }

        $generatedAt = now()->format('d F Y, H:i');

        $pdf = Pdf::loadView('pdf.laporan_penjualan', [
            'rows' => $rows,
            'generatedAt' => $generatedAt,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('laporan_penjualan.pdf');
    }

    public function downloadPeriode(Request $request): Response
    {
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');

        // Validasi parameter tanggal
        if (empty($tanggalMulai) || empty($tanggalAkhir)) {
            $html = '<html><body style="font-family: Arial, sans-serif; padding: 24px;">'
                . '<h2>Parameter tanggal tidak valid</h2>'
                . '<p>Silakan pilih periode tanggal terlebih dahulu.</p>'
                . '</body></html>';
            return response($html, 400);
        }

        // Ambil pesanan dalam periode tertentu beserta relasi pembayaran dan detail
        $pesanans = Pesanan::with(['pembayaran', 'detail'])
            ->whereBetween('tanggal_pesan', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('tanggal_pesan', 'asc')
            ->get();

        if ($pesanans->isEmpty()) {
            $html = '<html><body style="font-family: Arial, sans-serif; padding: 24px;">'
                . '<h2>Tidak ada data penjualan dalam periode tersebut</h2>'
                . '<p>Periode: ' . date('d F Y', strtotime($tanggalMulai)) . ' - ' . date('d F Y', strtotime($tanggalAkhir)) . '</p>'
                . '</body></html>';
            return response($html, 200);
        }

        // Susun data ringkas untuk tabel
        $rows = [];
        foreach ($pesanans as $p) {
            $totalItem = (int) $p->detail->sum('jumlah');
            $totalHarga = (float) $p->detail->sum('subtotal');
            $statusBayar = $p->pembayaran->status_pembayaran ?? 'belum dibayar';

            $rows[] = [
                'tanggal' => (string) ($p->tanggal_pesan ?? ''),
                'pelanggan' => (string) ($p->nama_pelanggan ?? ''),
                'total_item' => $totalItem,
                'total_harga' => $totalHarga,
                'status_pembayaran' => (string) $statusBayar,
            ];
        }

        $generatedAt = now()->format('d F Y, H:i');
        $periodeTeks = date('d F Y', strtotime($tanggalMulai)) . ' - ' . date('d F Y', strtotime($tanggalAkhir));

        $pdf = Pdf::loadView('pdf.laporan_penjualan', [
            'rows' => $rows,
            'generatedAt' => $generatedAt,
            'periode' => $periodeTeks,
        ])->setPaper('A4', 'portrait');

        // Format nama file dengan periode
        $fileName = 'laporan_penjualan_' . $tanggalMulai . '_sampai_' . $tanggalAkhir . '.pdf';

        return $pdf->download($fileName);
    }
}