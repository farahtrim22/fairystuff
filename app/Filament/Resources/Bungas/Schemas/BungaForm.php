<?php

namespace App\Filament\Resources\Bungas\Schemas;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

use function Laravel\Prompts\select;

class BungaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_bunga')
                    ->required(),
                TextInput::make('harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp. ')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $jenis = $get('jenis_bunga') ?? 'lokal';
                        $persen = $jenis === 'import' ? 0.2 : 0.3; // import 20%, lokal 30%
                        $set('keuntungan', ($state ?? 0) * $persen);
                    }),
                Select::make('jenis_bunga')
                    ->label('Kategori')
                    ->options([
                        'import' => 'Import',
                        'lokal' => 'Lokal',
                    ])
                    ->required()
                    // Reaktif agar menghitung ulang keuntungan saat kategori berubah
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $harga = $get('harga') ?? 0;
                        $persen = ($state === 'import') ? 0.2 : 0.3; // import 20%, lokal 30%
                        $set('keuntungan', $harga * $persen);
                    }),
                TextInput::make('jumlah_terjual')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(),
                Select::make('kualitas')
                    ->label('Kualitas (1-5)')
                    ->options([
                        '1' => '1- Sangat Buruk',
                        '2' => '2- Buruk',
                        '3' => '3- Cukup',
                        '4' => '4- Baik',
                        '5' => '5- Sangat Baik',
                    ])
                    ->required(),
                Select::make('popularitas')
                    ->label('Popularitas (1-5)')
                    ->options([
                        '1' => '1- Sangat Tidak Populer',
                        '2' => '2- Tidak Populer',
                        '3' => '3- Cukup Populer',
                        '4' => '4- Populer',
                        '5' => '5- Sangat Populer',
                    ])
                    ->required()
                    ->dehydrated(true),
                TextInput::make('keuntungan')
                    ->label('Keuntungan (otomatis dihitung)')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(true)
                    ->afterStateHydrated(function ($set, $get) {
                        $harga = $get('harga') ?? 0;
                        $jenis = $get('jenis_bunga') ?? 'lokal';
                        $persen = $jenis === 'import' ? 0.2 : 0.3; // import 20%, lokal 30%
                        $set('keuntungan', $harga * $persen);
                    }),
                TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->dehydrated(true),
                Select::make('status_bunga')
                ->options([
                    'tersedia' => 'tersedia',
                    'hampir habis' => 'hampir habis',
                    'habis' => 'habis',
                ]),
            ]);
    }
}
