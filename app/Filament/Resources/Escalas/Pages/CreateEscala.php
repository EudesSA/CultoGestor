<?php

namespace App\Filament\Resources\Escalas\Pages;

use App\Filament\Resources\Escalas\EscalaResource;
use App\Models\Escala;
use App\Models\Funcao;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEscala extends CreateRecord
{
    protected static string $resource = EscalaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        if (empty($data['culto_id'])) {
            Notification::make()
                ->title('Selecione um culto antes de salvar a escala.')
                ->danger()
                ->send();

            $this->halt();
        }

        return $this->salvarEquipe($data) ?? new Escala(['culto_id' => $data['culto_id']]);
    }

    private function salvarEquipe(array $data): ?Escala
    {
        $cultoId = $data['culto_id'];
        $funcoes = Funcao::where('ativo', true)->get();
        $ultima  = null;

        foreach ($funcoes as $funcao) {
            $userId = $data["funcao_{$funcao->id}"] ?? null;

            if ($userId) {
                $ultima = Escala::updateOrCreate(
                    ['culto_id' => $cultoId, 'funcao_id' => $funcao->id],
                    ['user_id' => $userId, 'status' => 'pendente']
                );
            } else {
                Escala::where('culto_id', $cultoId)
                    ->where('funcao_id', $funcao->id)
                    ->delete();
            }
        }

        return $ultima;
    }
}
