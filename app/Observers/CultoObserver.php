<?php

namespace App\Observers;

use App\Jobs\SincronizarGoogleCalendarJob;
use App\Models\Culto;

class CultoObserver
{
    public function created(Culto $culto): void
    {
        SincronizarGoogleCalendarJob::dispatch($culto->id, 'upsert')->onQueue('default');
    }

    public function updated(Culto $culto): void
    {
        $camposRelevantes = ['data', 'hora_inicio', 'hora_fim', 'tema', 'local', 'status', 'culto_tipo_id'];

        if ($culto->wasChanged($camposRelevantes)) {
            SincronizarGoogleCalendarJob::dispatch($culto->id, 'upsert')->onQueue('default');
        }
    }

    public function deleted(Culto $culto): void
    {
        SincronizarGoogleCalendarJob::dispatch($culto->id, 'delete')->onQueue('default');
    }
}
