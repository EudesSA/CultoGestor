<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Cantor;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $cantor = $this->record->cantor;

        if ($cantor) {
            $data['voz']         = $cantor->voz;
            $data['observacoes'] = $cantor->observacoes;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['voz'], $data['observacoes']);
        return $data;
    }

    protected function afterSave(): void
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
        } else {
            Cantor::where('user_id', $this->record->id)->update(['ativo' => false]);
        }
    }
}
