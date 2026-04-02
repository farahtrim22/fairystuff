<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bucket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'buckets';
    protected $fillable = [
        'nama_bucket',
        'deskripsi',
        'harga',
        'status',
        'foto_bucket',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function komposisi()
    {
        return $this->hasMany(KomposisiBucket::class, 'bucket_id');
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_bucket');
    }

    // Menghitung total bunga pada setiap bucket dari relasi komposisi
    public function getTotalBungaAttribute(): int
    {
        return (int) $this->komposisi()->sum('jumlah_bunga');
    }

    public function getFotoUrlAttribute()
    {
        return asset('storage/' . $this->foto_bucket);
    }

    public function shop()
    {
        $buckets = bucket::latest()->get();

        return view('customer.shop', compact('buckets'));
    }
}