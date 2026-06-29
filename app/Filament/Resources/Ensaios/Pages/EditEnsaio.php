<?php

namespace App\Filament\Resources\Ensaios\Pages;

use App\Filament\Resources\Ensaios\EnsaioResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEnsaio extends EditRecord
{
    protected static string $resource = EnsaioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
