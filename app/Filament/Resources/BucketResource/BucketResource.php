<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BucketResource\Pages;
use App\Filament\Resources\BucketResource\Pages\EditBucket;
use App\Filament\Resources\BucketResource\Pages\ListBuckets;
use App\Filament\Resources\BucketResource\Schemas\BucketForm;
use App\Filament\Resources\BucketResource\Tables\BucketsTable;
use App\Models\Bucket;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;


class BucketResource extends Resource
{
    protected static ?string $model = Bucket::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Buckets';

    public static function form(Schema $schema): Schema
    {
        // Delegasikan konfigurasi form ke kelas Schema terpisah
        return BucketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // Delegasikan konfigurasi tabel ke kelas Table terpisah
        return BucketsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBuckets::route('/'),
            'create' => Pages\CreateBucket::route('/create'),
            'edit' => Pages\EditBucket::route('/{record}/edit'),
        ];
    }
}
