<?php

namespace App\Filament\Resources\Hinos\Pages;

use App\Filament\Resources\Hinos\HinoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHinos extends ListRecords
{
    protected static string $resource = HinoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
