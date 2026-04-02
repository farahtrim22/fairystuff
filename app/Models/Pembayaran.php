<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Notifications\Notification;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';
    protected $primaryKey = 'id_pembayaran';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_pesanan',
        'tanggal_bayar',
        'metode',
        'status_pembayaran',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    protected static function booted(): void
    {
        static::updated(function ($pembayaran) {
            $pesanan = $pembayaran->pesanan;

            if (! $pesanan) {
                return;
            }

            // Pastikan field status_pembayaran benar-benar berubah (setelah update)
            if ($pembayaran->wasChanged('status_pembayaran')) {

                if ($pembayaran->status_pembayaran === 'belum_dibayar') {
                    // Sinkronisasi ke status pesanan 'menunggu'
                    $pesanan->status_pesanan = 'menunggu';
                    $pesanan->save();

                    // Notifikasi ke admin
                    Notification::make()
                        ->warning()
                        ->title("Status pesanan dikembalikan ke Menunggu ⏳ (Pesanan #{$pesanan->id_pesanan})")
                        ->send();
                } elseif ($pembayaran->status_pembayaran === 'dibayar') {
                    // Update status pesanan
                    $pesanan->status_pesanan = 'diproses';
                    $pesanan->save();

                    // Kurangi stok dan tambah jumlah terjual bunga berdasar komposisi bucket
                    foreach ($pesanan->detail as $detail) {
                        $bucket = $detail->bucket;
                        if (! $bucket) {
                            continue;
                        }
                        foreach ($bucket->komposisi as $komposisi) {
                            $bunga = $komposisi->bunga;
                            if (! $bunga) {
                                continue;
                            }

                            $jumlah = (int) ($komposisi->jumlah_bunga ?? 0) * (int) ($detail->jumlah ?? 0);
                            $bunga->stok = max(0, (int) $bunga->stok - $jumlah);
                            $bunga->jumlah_terjual = (int) $bunga->jumlah_terjual + $jumlah;

                            // Update status_bunga
                            if ($bunga->stok <= 0) {
                                $bunga->status_bunga = 'habis';
                            } elseif ($bunga->stok <= 30) {
                                $bunga->status_bunga = 'hampir habis';
                            } else {
                                $bunga->status_bunga = 'tersedia';
                            }

                            $bunga->save();
                        }
                    }

                    // Notifikasi ke admin
                    Notification::make()
                        ->success()
                        ->title("Status pesanan otomatis diperbarui menjadi Diproses ✅ (Pesanan #{$pesanan->id_pesanan})")
                        ->send();
                } elseif ($pembayaran->status_pembayaran === 'selesai') {
                    $pesanan->status_pesanan = 'selesai';
                    $pesanan->save();

                    // Notifikasi ke admin
                    Notification::make()
                        ->success()
                        ->title("Status pesanan otomatis diperbarui menjadi Selesai ✅ (Pesanan #{$pesanan->id_pesanan})")
                        ->send();
                }
            }
        });
    }
    
}