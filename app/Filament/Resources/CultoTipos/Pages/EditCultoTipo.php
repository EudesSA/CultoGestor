<?php

namespace App\Filament\Resources\CultoTipos\Pages;

use App\Filament\Resources\CultoTipos\CultoTipoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCultoTipo extends EditRecord
{
    protected static string $resource = CultoTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
