<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanans';
    protected $primaryKey = 'id_pesanan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_pelanggan',
        'tanggal_pesan',
        'total_harga',
        'status_pesanan',
        'kode_unik',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $pesanan) {
            if (! $pesanan->wasRecentlyCreated) {
                return; // hanya saat pesanan baru dibuat
            }

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
                    $bunga->save();
                }
            }
        });
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id_pesanan');
    }
}