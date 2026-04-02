<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bunga extends Model
{
    use SoftDeletes;
    protected $table = 'bungas';
    protected $primaryKey = 'id_bunga';
    public $incrementing = true;
    protected $keyType = 'int';

    // Perbaiki daftar kolom yang dapat diisi mass-assignment
    protected $fillable = [
        'nama_bunga',
        'jenis_bunga',
        'harga',
        'jumlah_terjual',
        'kualitas',
        'popularitas',
        'keuntungan',
        'stok',
        'status_bunga',
        'foto_bunga',
    ];

    // Cast tipe data agar konsisten dengan skema database
    protected $casts = [
        'harga' => 'decimal:2',
        'keuntungan' => 'decimal:2',
        'jumlah_terjual' => 'integer',
        'kualitas' => 'integer',
        'popularitas' => 'integer',
        'stok' => 'integer',
    ];

    protected static function booted(): void
    {
        // Hitung otomatis keuntungan saat menyimpan
        static::saving(function (self $bunga) {
            $harga = (float) ($bunga->harga ?? 0);
            $jenis = $bunga->jenis_bunga ?? 'lokal';
            // Rumus: import 20%, lokal 30%
            $persen = $jenis === 'import' ? 0.2 : 0.3;
            $bunga->keuntungan = $harga * $persen;
        });
    }

    public function komposisi()
    {
        return $this->hasMany(KomposisiBucket::class, 'id_bunga');
    }

    public function updateStatus()
    {
        if($this->stok < 10){
            $this->status_bunga = 'habis';
        } elseif($this->stok <= 30){
            $this->status_bunga = 'hampir habis';
        } else {
            $this->status_bunga = 'tersedia';
        }
        $this->save();
    }
}
