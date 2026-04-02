<?php

namespace App\Filament\Resources\Pesanans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PesananForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nama_pelanggan')
                ->label('Nama Pelanggan')
                ->required(),

            DatePicker::make('tanggal_pesan')
                ->label('Tanggal Pesan')
                ->default(now())
                ->required(),

            Select::make('status_pesanan')
                ->label('Status Pesanan')
                ->options([
                    'menunggu' => 'Menunggu',
                    'diproses' => 'Diproses',
                    'selesai' => 'Selesai',
                    'dibatalkan' => 'Dibatalkan',
                ])
                ->default('menunggu')
                ->required(),

            TextInput::make('total_harga')
                ->label('Total Harga')
                ->numeric()
                ->disabled()
                ->default(0),
        ]);
    }
}