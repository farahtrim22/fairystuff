<?php

namespace App\Filament\Resources\Bungas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BungasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_bunga')
                    ->searchable(),
                TextColumn::make('jenis_bunga')
                    ->searchable(),
                TextColumn::make('harga')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jumlah_terjual')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kualitas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('popularitas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('keuntungan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stok')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status_bunga')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
