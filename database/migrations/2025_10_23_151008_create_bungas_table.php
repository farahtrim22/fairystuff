<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bungas', function (Blueprint $table) {
            $table->id('id_bunga');
            $table->string('nama_bunga');
            $table->string('jenis_bunga')->nullable();
            $table->decimal('harga', 12, 2)->default(0);
            $table->unsignedInteger('jumlah_terjual')->default(0);
            $table->unsignedTinyInteger('kualitas')->nullable();
            $table->unsignedInteger('popularitas')->default(0);
            $table->decimal('keuntungan', 12, 2)->default(0);
            $table->unsignedInteger('stok')->default(0);
            $table->string('status_bunga')->default('tersedia');
            $table->string('foto_bunga')->nullable();
            $table->timestamps();

            $table->index('nama_bunga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bungas');
    }
};
