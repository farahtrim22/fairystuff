<?php

namespace App\Filament\Resources\Bungas\Pages;

use App\Filament\Resources\Bungas\BungaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBunga extends EditRecord
{
    protected static string $resource = BungaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavesNotificationTittle(): ?string
    {
        return 'Data Bunga berhasil diupdate!';
    }
}
