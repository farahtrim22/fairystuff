<?php

namespace App\Filament\Resources\DetailPesanans\Schemas;

use App\Models\Bucket;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DetailPesananForm
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
            Select::make('id_bucket')
                ->relationship('bucket', 'nama_bucket')
                ->label('Bucket')
                ->searchable()
                ->preload()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $bucket = Bucket::find($state);
                    $harga = (float) ($bucket->harga ?? 0);
                    $jumlah = (int) ($get('jumlah') ?? 0);
                    $set('subtotal', $harga * $jumlah);
                })
                ->required(),
            TextInput::make('jumlah')
                ->label('Jumlah')
                ->numeric()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $bucketId = $get('id_bucket');
                    $bucket = Bucket::find($bucketId);
                    $harga = (float) ($bucket->harga ?? 0);
                    $jumlah = (int) ($state ?? 0);
                    $set('subtotal', $harga * $jumlah);
                })
                ->required(),
            TextInput::make('subtotal')
                ->label('Subtotal (otomatis)')
                ->disabled()
                ->dehydrated(true)
                ->prefix('Rp. ')
                ->afterStateHydrated(function (callable $set, callable $get) {
                    $bucketId = $get('id_bucket');
                    $bucket = Bucket::find($bucketId);
                    $harga = (float) ($bucket->harga ?? 0);
                    $jumlah = (int) ($get('jumlah') ?? 0);
                    $set('subtotal', $harga * $jumlah);
                }),
        ]);
    }
}