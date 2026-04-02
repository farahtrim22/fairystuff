<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->string('nama_pelanggan');
            $table->date('tanggal_pesan');
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->string('status_pesanan')->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};