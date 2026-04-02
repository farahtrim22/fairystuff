<?php

namespace App\Filament\Resources\BucketResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class BucketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_bucket')
                    ->label('Foto')
                    ->disk('public')
                    ->visibility('public')
                    ->getStateUsing(fn ($record) => is_string($record->foto_bucket) && $record->foto_bucket !== '' ? $record->foto_bucket : null)
                    ->square()
                    ->size(64),
                TextColumn::make('nama_bucket')
                    ->label('Nama Bouquet')
                    ->searchable(),
                TextColumn::make('harga')
                    ->label('Harga')
                    ->numeric(),
                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'nonaktif',
                    ]),
                TextColumn::make('komposisi_count')
                    ->label('Jumlah Komposisi')
                    ->counts('komposisi'),
                TextColumn::make('total_bunga')
                    ->label('Total Bunga')
                    ->sortable()
                    ->numeric(),
            ])
            ->filters([
                // Tambahkan filter bila perlu
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}