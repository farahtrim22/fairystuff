<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Halaman konfirmasi pembayaran.
     */
    public function show(int $id)
    {
        $pesanan = Pesanan::with('pembayaran')->findOrFail($id);
        $pembayaran = $pesanan->pembayaran;
        return view('customer.payment', compact('pesanan', 'pembayaran'));
    }

    /**
     * Update status pembayaran.
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'status_pembayaran' => ['required','in:Belum Dibayar,Menunggu Verifikasi,Dibayar'],
            'metode' => ['nullable','string','max:50'],
        ]);

        $pembayaran = Pembayaran::where('id_pesanan', $id)->firstOrFail();
        if (!empty($validated['metode'])) {
            $pembayaran->metode = $validated['metode'];
        }
        $pembayaran->status_pembayaran = $validated['status_pembayaran'];
        $pembayaran->save();

        return redirect()->route('payment.show', ['id' => $id])
            ->with('success', 'Status pembayaran diperbarui.');
    }

    /**
     * Tampilkan form upload bukti transfer (GET /payment-confirmation/{id}).
     */
    public function showForm(int $id)
    {
        $pesanan = Pesanan::with('pembayaran')->findOrFail($id);
        $pembayaran = $pesanan->pembayaran;
        return view('customer.payment-confirmation', compact('pesanan', 'pembayaran'));
    }

    /**
     * Tampilkan halaman konfirmasi berdasarkan kode unik (GET /payment-confirmation/{kode_unik}).
     */
    public function showByCode(string $kode_unik)
    {
        $pesanan = Pesanan::where('kode_unik', $kode_unik)->with('pembayaran')->firstOrFail();
        $pembayaran = $pesanan->pembayaran;
        return view('customer.payment-confirmation', compact('pesanan', 'pembayaran'));
    }

    /**
     * Upload bukti transfer (POST /payment-confirmation/{id}).
     */
    public function uploadProof(Request $request, int $id)
    {
        $validated = $request->validate([
            'bukti_transfer' => ['required','file','mimes:jpg,jpeg,png','max:2048'],
        ]);

        $pesanan = Pesanan::findOrFail($id);
        $pembayaran = Pembayaran::where('id_pesanan', $pesanan->id_pesanan)->firstOrFail();

        // Simpan file ke storage/app/public/payments
        $path = $request->file('bukti_transfer')->store('payments', 'public');

        // Update kolom bukti_transfer dan status
        $pembayaran->bukti_transfer = $path;
        $pembayaran->status_pembayaran = 'Menunggu Verifikasi';
        $pembayaran->tanggal_bayar = now();
        $pembayaran->save();

        // Update status pesanan menjadi Menunggu
        $pesanan->status_pesanan = 'Menunggu';
        $pesanan->save();

        return redirect()->route('payment.confirmation.show', ['id' => $id])
            ->with('success', 'Bukti transfer berhasil diunggah. Menunggu verifikasi admin.');
    }
}