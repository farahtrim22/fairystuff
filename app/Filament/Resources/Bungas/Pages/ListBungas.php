<?php

namespace App\Filament\Resources\Bungas\Pages;

use App\Filament\Resources\Bungas\BungaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBungas extends ListRecords
{
    protected static string $resource = BungaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
