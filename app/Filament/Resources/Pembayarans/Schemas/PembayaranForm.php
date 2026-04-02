<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PembayaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('id_pesanan')
                ->relationship('pesanan', 'nama_pelanggan')
                ->label('Pesanan')
                ->searchable()
                ->preload()
                ->required(),

            DatePicker::make('tanggal_bayar')
                ->label('Tanggal Bayar')
                ->required(),

            TextInput::make('metode')
                ->label('Metode')
                ->maxLength(100)
                ->required(),

            Select::make('status_pembayaran')
                ->label('Status Pembayaran')
                ->options([
                    'Belum Dibayar' => 'Belum Dibayar',
                    'Menunggu Verifikasi' => 'Menunggu Verifikasi',
                    'Dibayar' => 'Dibayar',
                ])
                ->default('Belum Dibayar'),
        ]);
    }
}