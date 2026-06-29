<?php

namespace App\Jobs;

use App\Models\Escala;
use App\Notifications\EscalaLembreteNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

/**
 * Aviso automático 3 dias antes do culto (Especificação Mestra, Módulo 1).
 *
 * Agendado diariamente no scheduler (routes/console.php). Para cada culto
 * agendado cuja data seja "hoje + 3 dias", notifica os escalados que ainda
 * estão com status `pendente`.
 */
class NotificarEscalaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): int
    {
        $dataAlvo = Carbon::today()->addDays(3)->toDateString();

        $escalas = Escala::query()
            ->where('status', 'pendente')
            ->whereHas('culto', fn ($q) => $q
                ->whereDate('data', $dataAlvo)
                ->where('status', 'agendado'))
            ->with(['culto.tipo', 'funcao', 'user'])
            ->get();

        $enviados = 0;
        foreach ($escalas as $escala) {
            if ($escala->user?->email) {
                $escala->user->notify(new EscalaLembreteNotification($escala));
                $enviados++;
            }
        }

        return $enviados;
    }
}
