<?php

namespace App\Filament\Resources\Funcaos\Pages;

use App\Filament\Resources\Funcaos\FuncaoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFuncao extends EditRecord
{
    protected static string $resource = FuncaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
