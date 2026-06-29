<?php

namespace App\Filament\Resources\Funcaos\Pages;

use App\Filament\Resources\Funcaos\FuncaoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFuncaos extends ListRecords
{
    protected static string $resource = FuncaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
