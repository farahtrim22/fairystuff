<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('buckets', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bucket');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2)->default(0);
            $table->string('foto_bucket')->nullable();
            $table->string('status')->default('aktif'); // aktif / nonaktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buckets');
    }
};