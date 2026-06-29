<?php

namespace App\Filament\Resources\Ensaios\Pages;

use App\Filament\Resources\Ensaios\EnsaioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEnsaios extends ListRecords
{
    protected static string $resource = EnsaioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
