<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_pesanans', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_pesanan')
                ->constrained(table: 'pesanans', column: 'id_pesanan')
                ->OnDelete('cascade');
            $table->foreignId('id_bucket')
                ->constrained(table: 'buckets', column: 'id')
                ->OnDelete('cascade');
            $table->Integer('jumlah');
            $table->decimal('subtotal', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanans');
    }
};