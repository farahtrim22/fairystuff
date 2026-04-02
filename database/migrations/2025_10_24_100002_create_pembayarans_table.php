<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_pesanan')
                ->constrained(table: 'pesanans', column: 'id_pesanan')
                ->cascadeOnDelete();
            $table->date('tanggal_bayar')->nullable();
            $table->string('metode')->nullable();
            $table->string('status_pembayaran')->default('belum dibayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};