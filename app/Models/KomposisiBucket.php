<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomposisiBucket extends Model
{
    use HasFactory;

    protected $table = 'komposisi_buckets';
    protected $fillable = [
        'bucket_id',
        'id_bunga',
        'jumlah_bunga',
    ];

    public function bunga()
    {
        return $this->belongsTo(Bunga::class, 'id_bunga');
    }
}