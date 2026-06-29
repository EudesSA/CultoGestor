<?php

namespace App\Filament\Resources\CultoTipos\Pages;

use App\Filament\Resources\CultoTipos\CultoTipoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCultoTipos extends ListRecords
{
    protected static string $resource = CultoTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
