<?php

namespace App\Filament\Resources\Musicas\Pages;

use App\Filament\Resources\Musicas\MusicaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMusica extends CreateRecord
{
    protected static string $resource = MusicaResource::class;

    /**
     * Blindagem de servidor: um Cantor restrito nunca cria música em nome de
     * outro cantor nem define o próprio status (campos desabilitados na UI,
     * mas o estado do Livewire é manipulável pelo cliente).
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (MusicaResource::cantorRestrito()) {
            $data['cantor_id'] = auth()->user()?->cantor?->id;
            $data['status']    = 'pendente';
        }

        return $data;
    }
}
