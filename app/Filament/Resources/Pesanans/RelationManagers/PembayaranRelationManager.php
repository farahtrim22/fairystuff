<?php

namespace App\Filament\Resources\Pesanans\RelationManagers;

use App\Models\Pesanan;
use App\Models\Pembayaran;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PembayaranRelationManager extends RelationManager
{
    protected static string $relationship = 'pembayaran';
    protected static ?string $title = 'Pembayaran';

    public function form(Schema $schema): Schema
    {
        // Read-only; admin hanya melakukan verifikasi via aksi
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('metode')
                    ->label('Metode')
                    ->searchable(),
                TextColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->colors([
                        'gray' => 'Belum Dibayar',
                        'warning' => 'Menunggu Verifikasi',
                        'success' => 'Dibayar',
                    ])
                    ->sortable(),
                TextColumn::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->date('Y-m-d')
                    ->sortable(),
                ImageColumn::make('bukti_transfer')
                    ->label('Bukti Transfer')
                    ->disk('public')
                    ->square()
                    ->extraAttributes(['class' => 'cursor-zoom-in']),
                TextColumn::make('bukti_transfer')
                    ->label('Lihat Bukti')
                    ->formatStateUsing(function ($state, $record) {
                        $url = $record?->bukti_transfer ? asset('storage/' . $record->bukti_transfer) : null;
                        return $url
                            ? '<a href="' . e($url) . '" target="_blank" class="text-primary underline">Lihat</a>'
                            : '-';
                    })
                    ->html(),
            ])
            ->headerActions([])
            ->recordActions([
                Action::make('verify')
                    ->label('Verifikasi Pembayaran')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Pembayaran $record) {
                        $record->status_pembayaran = 'Dibayar';
                        $record->tanggal_bayar = now();
                        $record->save();

                        // Update status pesanan
                        $pesanan = $record->pesanan;
                        if ($pesanan) {
                            $pesanan->status_pesanan = 'diproses';
                            $pesanan->save();
                        }

                        Notification::make()
                            ->title('Pembayaran terverifikasi dan pesanan diproses ✅')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }
}