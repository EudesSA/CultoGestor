<?php

namespace App\Filament\Resources\Cantors\Pages;

use App\Filament\Resources\Cantors\CantorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCantors extends ListRecords
{
    protected static string $resource = CantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
