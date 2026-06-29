<?php

namespace App\Observers;

use App\Jobs\SincronizarGoogleCalendarJob;
use App\Models\HistoricoCantor;
use App\Models\Musica;
use App\Notifications\CantorAgendadoNotification;

class MusicaObserver
{
    public function created(Musica $musica): void
    {
        // Música de repertório (sem culto) não afeta o Google Calendar.
        if ($musica->culto_id) {
            SincronizarGoogleCalendarJob::dispatch($musica->culto_id, 'upsert')->onQueue('default');
        }
    }

    public function updated(Musica $musica): void
    {
        // Ao aprovar: notifica o cantor e registra o histórico imutável.
        if ($musica->wasChanged('status') && $musica->status === 'aprovado') {
            $musica->load(['culto.tipo', 'cantor.user']);

            $this->registrarHistorico($musica);

            $user = $musica->cantor?->user;
            if ($user?->email) {
                $user->notify(new CantorAgendadoNotification($musica));
            }
        }
    }

    /**
     * Grava uma entrada em historico_cantores (log append-only — nunca
     * atualizar nem deletar; Especificação Mestra, princípio "Histórico imutável").
     * Idempotente por música para não duplicar em reaprovações.
     */
    private function registrarHistorico(Musica $musica): void
    {
        // O histórico registra músicas cantadas EM UM CULTO. Música de
        // repertório (sem culto) só gera entrada quando for associada a um.
        if (! $musica->culto_id) {
            return;
        }

        $culto = $musica->culto;

        HistoricoCantor::firstOrCreate(
            ['musica_id' => $musica->id],
            [
                'cantor_id'   => $musica->cantor_id,
                'culto_id'    => $musica->culto_id,
                'nome_musica' => $musica->nome,
                'artista'     => $musica->artista,
                'tom'         => $musica->tom,
                'data_culto'  => $culto?->data,
                'tipo_culto'  => $culto?->tipo?->nome,
            ],
        );
    }

    public function deleted(Musica $musica): void
    {
        if ($musica->culto_id) {
            SincronizarGoogleCalendarJob::dispatch($musica->culto_id, 'upsert')->onQueue('default');
        }
    }
}
