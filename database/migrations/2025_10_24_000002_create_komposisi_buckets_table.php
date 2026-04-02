<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Jika tabel sudah ada karena migrasi sebelumnya gagal di constraint, drop dulu
        Schema::dropIfExists('komposisi_buckets');

        Schema::create('komposisi_buckets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bucket_id')->constrained('buckets')->cascadeOnDelete();
            $table->foreignId('id_bunga')->constrained(table: 'bungas', column: 'id_bunga');
            $table->unsignedInteger('jumlah_bunga');
            $table->string('proporsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komposisi_buckets');
    }
};