<?php

namespace App\Filament\Resources\Hinos\Pages;

use App\Filament\Resources\Hinos\HinoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHino extends EditRecord
{
    protected static string $resource = HinoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
