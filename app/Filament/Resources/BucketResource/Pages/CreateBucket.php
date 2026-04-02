<?php

namespace App\Filament\Resources\BucketResource\Pages;

use App\Filament\Resources\BucketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBucket extends CreateRecord
{
    protected static string $resource = BucketResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}