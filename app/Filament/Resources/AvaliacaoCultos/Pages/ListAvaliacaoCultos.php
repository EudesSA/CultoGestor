<?php

namespace App\Filament\Resources\AvaliacaoCultos\Pages;

use App\Filament\Resources\AvaliacaoCultos\AvaliacaoCultoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAvaliacaoCultos extends ListRecords
{
    protected static string $resource = AvaliacaoCultoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
