<?php

namespace App\Observers;

use App\Models\EnsaioParticipante;
use App\Notifications\EnsaioConvocacaoNotification;

class EnsaioParticipanteObserver
{
    public function created(EnsaioParticipante $participante): void
    {
        $participante->load(['ensaio.culto', 'ensaio.musicas', 'user']);

        if ($participante->user?->email) {
            $participante->user->notify(new EnsaioConvocacaoNotification($participante));
        }
    }
}
