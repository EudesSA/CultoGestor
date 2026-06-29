<?php

namespace App\Filament\Resources\Cantors\Pages;

use App\Filament\Resources\Cantors\CantorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCantor extends EditRecord
{
    protected static string $resource = CantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
