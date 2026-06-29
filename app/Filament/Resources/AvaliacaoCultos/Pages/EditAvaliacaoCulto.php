<?php

namespace App\Filament\Resources\AvaliacaoCultos\Pages;

use App\Filament\Resources\AvaliacaoCultos\AvaliacaoCultoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAvaliacaoCulto extends EditRecord
{
    protected static string $resource = AvaliacaoCultoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
