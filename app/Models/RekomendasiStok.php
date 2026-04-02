<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekomendasiStok extends Model
{
    use HasFactory;

    protected $table = 'rekomendasi_stok';

    protected $fillable = [
        'id_bunga',
        'nilai_preferensi',
        'ranking',
        'status_rekomendasi',
    ];

    protected $casts = [
        'nilai_preferensi' => 'decimal:4',
    ];

    public function bunga(): BelongsTo
    {
        return $this->belongsTo(Bunga::class, 'id_bunga', 'id_bunga');
    }
}