<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            if (!Schema::hasColumn('pesanans', 'kode_unik')) {
                $table->string('kode_unik', 32)->nullable()->unique()->after('id_pesanan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            if (Schema::hasColumn('pesanans', 'kode_unik')) {
                $table->dropUnique(['kode_unik']);
                $table->dropColumn('kode_unik');
            }
        });
    }
};