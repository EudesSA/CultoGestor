<?php

namespace App\Filament\Resources\Musicas\Pages;

use App\Filament\Resources\Musicas\MusicaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMusicas extends ListRecords
{
    protected static string $resource = MusicaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
