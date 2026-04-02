<?php

namespace App\Http\Controllers;

use App\Models\Bucket;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Tampilkan halaman checkout (GET /checkout).
     */
    public function index(Request $request)
    {
        $cart = Session::get('cart', []);
        $total = collect($cart)->sum(function ($item) {
            return ($item['harga'] ?? 0) * ($item['qty'] ?? 0);
        });
        return view('customer.checkout', compact('cart', 'total'));
    }

    /**
     * Tampilkan keranjang.
     */
    public function cart()
    {
        $cart = Session::get('cart', []);
        $total = collect($cart)->sum(function ($item) {
            return ($item['harga'] ?? 0) * ($item['qty'] ?? 0);
        });
        return view('customer.cart', compact('cart', 'total'));
    }

    /**
     * Tambah item ke keranjang.
     */
    public function add(Request $request)
    {
        $data = $request->validate([
            'id' => ['required','integer','exists:buckets,id'],
            'qty' => ['nullable','integer','min:1'],
        ]);

        $bucket = Bucket::findOrFail($data['id']);
        $qty = (int)($data['qty'] ?? 1);

        $cart = Session::get('cart', []);
        if (!isset($cart[$bucket->id])) {
            $cart[$bucket->id] = [
                'id' => $bucket->id,
                'nama' => $bucket->nama_bucket,
                'harga' => (float)$bucket->harga,
                'foto' => $bucket->foto_bucket,
                'qty' => 0,
            ];
        }
        $cart[$bucket->id]['qty'] += $qty;
        Session::put('cart', $cart);

        return redirect()->route('cart')->with('success', 'Berhasil menambahkan ke keranjang');
    }

    /**
     * Update quantity item di keranjang.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => ['required','integer'],
            'qty' => ['required','integer'],
        ]);

        $id = (int)$data['id'];
        $qty = (int)$data['qty'];

        $cart = Session::get('cart', []);
        if (!isset($cart[$id])) {
            return redirect()->back()->with('error', 'Item tidak ditemukan');
        }

        if ($qty <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id]['qty'] = $qty;
        }

        Session::put('cart', $cart);
        return redirect()->back()->with('success', 'Keranjang diperbarui');
    }

    /**
     * Hapus item dari keranjang.
     */
    public function remove(int $id)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
            return redirect()->back()->with('success', 'Item dihapus dari keranjang');
        }
        return redirect()->back()->with('error', 'Item tidak ditemukan di keranjang');
    }

    /**
     * Proses checkout (POST /checkout): simpan pesanan & detail.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required','string','max:100'],
            'alamat' => ['required','string','max:255'],
            'kota' => ['nullable','string','max:100'],
            'kode_pos' => ['nullable','string','max:20'],
            'whatsapp' => ['nullable','string','max:30'],
            'metode' => ['required','string','max:50'],
        ]);

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong');
        }

        // Hitung total: subtotal + service fee 2%
        $subtotal = collect($cart)->sum(function ($item) {
            return ((float)($item['harga'] ?? 0)) * ((int)($item['qty'] ?? 0));
        });
        $serviceFee = $subtotal * 0.02;
        $grandTotal = $subtotal + $serviceFee;

        // Buat pesanan (sesuai skema tabel saat ini)
        $pesanan = Pesanan::create([
            'nama_pelanggan' => $validated['nama_lengkap'],
            'tanggal_pesan' => now(),
            'total_harga' => $grandTotal,
            'status_pesanan' => 'Menunggu',
        ]);

        // Generate kode unik: FSF-[YYYYMMDD]-[4 CHAR]
        $kode_unik = 'FSF-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        $pesanan->kode_unik = $kode_unik;
        $pesanan->save();

        // Detail pesanan
        foreach ($cart as $item) {
            DetailPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'id_bucket' => $item['id'],
                'jumlah' => (int)($item['qty'] ?? 0),
                'subtotal' => ((float)($item['harga'] ?? 0)) * ((int)($item['qty'] ?? 0)),
            ]);
        }

        // Buat data pembayaran awal
        Pembayaran::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'tanggal_bayar' => null,
            'metode' => $validated['metode'],
            'status_pembayaran' => 'Belum Dibayar',
        ]);

        // Kosongkan keranjang
        Session::forget('cart');

        // Kirim WhatsApp otomatis via Fonnte jika nomor tersedia
        try {
            $target = $validated['whatsapp'] ?? $request->get('nomor_wa');
            if (!empty($target) && env('FONNTE_API_KEY')) {
                $confirmUrl = 'https://fairystuff.id/payment-confirmation/' . $pesanan->kode_unik;
                $message = "🌸 Halo Kak, terima kasih sudah memesan di Fairystuff Florist 💐\n"
                    . "Nomor Pesanan: #{$pesanan->id_pesanan}\n"
                    . "Kode Unik: {$pesanan->kode_unik}\n"
                    . "Total Pembayaran: Rp " . number_format($pesanan->total_harga,0,',','.') . "\n\n"
                    . "Klik link berikut untuk konfirmasi pembayaran dan memantau status pesanan:\n"
                    . "👉 {$confirmUrl}";

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://api.fonnte.com/send',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => [
                        'target' => $target,
                        'message' => $message,
                    ],
                    CURLOPT_HTTPHEADER => [
                        'Authorization: ' . env('FONNTE_API_KEY'),
                    ],
                ]);
                $response = curl_exec($curl);
                curl_close($curl);
                // Tandai bahwa WA sudah dikirim
                Session::flash('whatsapp_sent', true);
            }
        } catch (\Throwable $e) {
            // Jangan menggagalkan checkout jika WA gagal; cukup log ringan
            Log::warning('WA Gateway error: ' . $e->getMessage());
        }

        // Redirect ke halaman konfirmasi pembayaran berbasis kode unik
        return redirect()->route('payment.confirmation.code', ['kode_unik' => $pesanan->kode_unik])
            ->with('success', 'Pesanan berhasil dibuat. Kode unik telah dikirim ke WhatsApp.');
    }

    /**
     * Tampilkan status pesanan dan pembayaran terkait.
     */
    public function status($id)
    {
        $pesanan = Pesanan::with('pembayaran')->findOrFail($id);
        $pembayaran = $pesanan->pembayaran;

        // Tampilkan halaman gabungan konfirmasi + status
        return view('customer.payment-confirmation', compact('pesanan', 'pembayaran'));
    }
}