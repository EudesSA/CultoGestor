<?php

namespace App\Filament\Resources\Musicas\Pages;

use App\Filament\Resources\Musicas\MusicaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMusica extends EditRecord
{
    protected static string $resource = MusicaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Blindagem de servidor: ao editar, um Cantor restrito não troca o cantor
     * dono nem altera o status (mantém o que está no banco).
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (MusicaResource::cantorRestrito()) {
            $data['cantor_id'] = $this->record->cantor_id;
            unset($data['status']);
        }

        return $data;
    }
}
