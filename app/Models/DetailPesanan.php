<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    use HasFactory;

    protected $table = 'detail_pesanans';
    protected $primaryKey = 'id_detail';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'id_bucket',
        'jumlah',
        'subtotal',
    ];

    protected static function booted()
    {
        static::created(function ($detail) {
            $bucket = $detail->bucket;
            foreach($bucket->komposisi as $komposisi){
                $bunga = $komposisi->bunga;

                $jumlahTerpakai = $komposisi->jumlah_bunga * $detail->jumlah;
                // Kurangi stok dan tambah jumlah terjual sesuai komposisi * jumlah bucket
                $bunga->stok = max(0, (int) $bunga->stok - (int) $jumlahTerpakai);
                $bunga->jumlah_terjual = (int) $bunga->jumlah_terjual + (int) $jumlahTerpakai;

                // Update status berdasarkan stok terkini
                if ($bunga->stok <= 0) {
                    $bunga->status_bunga = 'habis';
                } elseif ($bunga->stok <= 30) {
                    $bunga->status_bunga = 'hampir habis';
                } else {
                    $bunga->status_bunga = 'tersedia';
                }
                $bunga->save();
            }
        });
    }

    public function bucket()
    {
        return $this->belongsTo(Bucket::class, 'id_bucket');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}