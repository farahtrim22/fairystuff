<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rekomendasi_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bunga')
                ->constrained(table: 'bungas', column: 'id_bunga')
                ->cascadeOnDelete();
            $table->decimal('nilai_preferensi', 8, 4);
            $table->integer('ranking');
            $table->string('status_rekomendasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekomendasi_stok');
    }
};