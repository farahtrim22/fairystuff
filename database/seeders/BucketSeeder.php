<?php

namespace Database\Seeders;

use App\Models\Bucket;
use App\Models\Bunga;
use App\Models\KomposisiBucket;
use Illuminate\Database\Seeder;

class BucketSeeder extends Seeder
{
    public function run(): void
    {
        // Buat bucket contoh
        $bucket = Bucket::updateOrCreate(
            ['nama_bucket' => 'Romantic Bouquet'],
            [
                'deskripsi' => 'Buketan romantis untuk hadiah spesial',
                'harga' => 150000,
                'status' => 'aktif',
                'foto_bucket' => null,
            ]
        );

        // Ambil bunga yang sudah di-seed
        $mawar = Bunga::where('nama_bunga', 'Mawar')->first();
        $melati = Bunga::where('nama_bunga', 'Melati')->first();

        if ($mawar) {
            KomposisiBucket::updateOrCreate(
                ['bucket_id' => $bucket->id, 'id_bunga' => $mawar->id_bunga],
                ['jumlah_bunga' => 5]
            );
        }

        if ($melati) {
            KomposisiBucket::updateOrCreate(
                ['bucket_id' => $bucket->id, 'id_bunga' => $melati->id_bunga],
                ['jumlah_bunga' => 3]
            );
        }
    }
}