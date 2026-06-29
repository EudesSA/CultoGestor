<?php

namespace App\Observers;

use App\Jobs\SincronizarGoogleCalendarJob;
use App\Models\Escala;
use App\Notifications\EscalaConfirmacaoNotification;

class EscalaObserver
{
    public function created(Escala $escala): void
    {
        SincronizarGoogleCalendarJob::dispatch($escala->culto_id, 'upsert')->onQueue('default');

        // Notifica o membro escalado por email
        $escala->load(['culto.tipo', 'funcao', 'user']);
        if ($escala->user?->email) {
            $escala->user->notify(new EscalaConfirmacaoNotification($escala));
        }
    }

    public function deleted(Escala $escala): void
    {
        SincronizarGoogleCalendarJob::dispatch($escala->culto_id, 'upsert')->onQueue('default');
    }
}
