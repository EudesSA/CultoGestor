<?php

namespace App\Filament\Resources\Escalas\Pages;

use App\Filament\Resources\Escalas\EscalaResource;
use App\Models\Escala;
use App\Models\Funcao;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditEscala extends EditRecord
{
    protected static string $resource = EscalaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $cultoId = $this->record->culto_id;
        $escalas = Escala::where('culto_id', $cultoId)->get()->keyBy('funcao_id');

        $data['culto_id'] = $cultoId;

        foreach ($escalas as $funcaoId => $escala) {
            $data["funcao_{$funcaoId}"] = $escala->user_id;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (empty($data['culto_id'])) {
            Notification::make()
                ->title('Selecione um culto antes de salvar a escala.')
                ->danger()
                ->send();

            $this->halt();
        }

        $cultoId = $data['culto_id'];
        $funcoes = Funcao::where('ativo', true)->get();

        foreach ($funcoes as $funcao) {
            $userId = $data["funcao_{$funcao->id}"] ?? null;

            if ($userId) {
                Escala::updateOrCreate(
                    ['culto_id' => $cultoId, 'funcao_id' => $funcao->id],
                    ['user_id' => $userId, 'status' => 'pendente']
                );
            } else {
                Escala::where('culto_id', $cultoId)
                    ->where('funcao_id', $funcao->id)
                    ->delete();
            }
        }

        return $record;
    }
}
