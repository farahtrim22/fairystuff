<?php

namespace App\Filament\Resources\BucketResource\Pages;

use App\Filament\Resources\BucketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBucket extends EditRecord
{
    protected static string $resource = BucketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data Bucket Berhasil Diupdate!';
    }
}