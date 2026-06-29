<?php

namespace App\Filament\Resources\Cultos\Pages;

use App\Filament\Resources\Cultos\CultoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCultos extends ListRecords
{
    protected static string $resource = CultoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
