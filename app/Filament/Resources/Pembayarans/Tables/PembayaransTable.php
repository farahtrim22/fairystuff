<?php

namespace App\Filament\Resources\Pembayarans\Tables;

use App\Models\Pembayaran;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PembayaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pesanan.nama_pelanggan')
                    ->label('Pelanggan')
                    ->searchable(),
                ImageColumn::make('bukti_transfer')
                    ->label('Bukti Transfer')
                    ->disk('public')
                    ->square(),
                TextColumn::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('metode')
                    ->label('Metode')
                    ->searchable(),
                TextColumn::make('status_pembayaran')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status_pembayaran')
                    ->label('Filter Status')
                    ->options([
                        'Belum Dibayar' => 'Belum Dibayar',
                        'Menunggu Verifikasi' => 'Menunggu Verifikasi',
                        'Dibayar' => 'Dibayar',
                    ]),
            ])
            ->recordActions([
                Action::make('verify')
                    ->label('Verifikasi Pembayaran')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Pembayaran $record) {
                        $record->status_pembayaran = 'Dibayar';
                        if (! $record->tanggal_bayar) {
                            $record->tanggal_bayar = now();
                        }
                        $record->save();

                        // Update status pesanan otomatis menjadi Diproses
                        if ($record->pesanan) {
                            $record->pesanan->status_pesanan = 'Diproses';
                            $record->pesanan->save();
                        }
                    }),
            ]);
    }
}