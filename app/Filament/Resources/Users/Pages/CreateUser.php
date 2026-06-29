<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Cantor;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['voz'], $data['observacoes']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncCantorPerfil();
    }

    private function syncCantorPerfil(): void
    {
        $isCantor = $this->record->roles()->where('name', 'Cantor')->exists();

        if ($isCantor) {
            Cantor::updateOrCreate(
                ['user_id' => $this->record->id],
                [
                    'nome'        => $this->record->name,
                    'email'       => $this->record->email,
                    'voz'         => $this->data['voz'] ?? null,
                    'observacoes' => $this->data['observacoes'] ?? null,
                    'ativo'       => true,
                ]
            );
        }
    }
}
