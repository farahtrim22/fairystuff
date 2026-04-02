<?php

namespace App\Filament\Resources\BucketResource\Schemas;

use App\Models\Bunga;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BucketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_bucket')
                    ->label('Nama Bucket')
                    ->required()
                    ->maxLength(255),
                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                TextInput::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp. '),
                FileUpload::make('foto_bucket')
                    ->label('Foto')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('buckets'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ])
                    ->default('aktif'),

                Repeater::make('komposisi')
                    ->relationship('komposisi')
                    ->label('Komposisi Bunga')
                    ->columns(3)
                    ->schema([
                        Select::make('id_bunga')
                            ->label('Pilih Bunga')
                            ->options(function () {
                                return Bunga::query()->pluck('nama_bunga', 'id_bunga');
                            })
                            ->searchable()
                            ->required(),
                        TextInput::make('jumlah_bunga')
                            ->label('Jumlah')
                            ->numeric()
                            ->required(),
                    ])
                    ->createItemButtonLabel('Tambah Bunga'),
            ]);
    }
}