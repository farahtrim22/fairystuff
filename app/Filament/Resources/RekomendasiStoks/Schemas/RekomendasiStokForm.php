<?php

namespace App\Filament\Resources\RekomendasiStoks\Schemas;

use Filament\Schemas\Schema;

class RekomendasiStokForm
{
    public static function configure(Schema $schema): Schema
    {
        // Tidak ada form khusus; return kosong untuk konsistensi struktur
        return $schema->components([]);
    }
}