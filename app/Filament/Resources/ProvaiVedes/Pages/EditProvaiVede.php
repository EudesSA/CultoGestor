<?php

namespace App\Filament\Resources\ProvaiVedes\Pages;

use App\Filament\Resources\ProvaiVedes\ProvaiVedeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProvaiVede extends EditRecord
{
    protected static string $resource = ProvaiVedeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
