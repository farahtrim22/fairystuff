<?php

namespace App\Filament\Resources\Bungas\Pages;

use App\Filament\Resources\Bungas\BungaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBunga extends CreateRecord
{
    protected static string $resource = BungaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
