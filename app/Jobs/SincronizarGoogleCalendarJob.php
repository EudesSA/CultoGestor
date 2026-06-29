<?php

namespace App\Jobs;

use App\Models\Culto;
use App\Models\GoogleCalendarEvento;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;

class SincronizarGoogleCalendarJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public readonly int $cultoId,
        public readonly string $acao = 'upsert', // upsert | delete
    ) {}

    public function handle(): void
    {
        if (! $this->credenciaisConfiguradas()) {
            Log::info('SincronizarGoogleCalendarJob: credenciais não configuradas, pulando.');
            return;
        }

        $culto = Culto::with([
            'tipo',
            'escalas.user',
            'escalas.funcao',
            'googleCalendarEvento',
        ])->find($this->cultoId);

        if (! $culto) {
            return;
        }

        try {
            if ($this->acao === 'delete' || $culto->status === 'cancelado') {
                $this->excluirEvento($culto);
                return;
            }

            $this->upsertEvento($culto);
        } catch (\Throwable $e) {
            Log::error("SincronizarGoogleCalendarJob: erro no culto #{$this->cultoId}: {$e->getMessage()}");
            throw $e;
        }
    }

    private function upsertEvento(Culto $culto): void
    {
        $titulo = ($culto->tipo?->nome ?? 'Culto')
            . ($culto->tema ? ' — ' . $culto->tema : '');

        $inicio = Carbon::parse(
            $culto->data->toDateString() . ' ' . ($culto->hora_inicio ?? '00:00:00')
        );
        $fim = Carbon::parse(
            $culto->data->toDateString() . ' ' . ($culto->hora_fim ?? $culto->hora_inicio ?? '00:00:00')
        );
        if ($fim->lte($inicio)) {
            $fim = $inicio->copy()->addHours(2);
        }

        $descricao  = $this->montarDescricao($culto);
        $convidados = $this->coletarConvidados($culto);

        $eventoExistente = $culto->googleCalendarEvento;

        if ($eventoExistente?->google_event_id) {
            try {
                $evento = Event::find($eventoExistente->google_event_id);

                $evento->name          = $titulo;
                $evento->description   = $descricao;
                $evento->location      = $culto->local ?? '';
                $evento->startDateTime = $inicio;
                $evento->endDateTime   = $fim;

                foreach ($convidados as $c) {
                    $evento->addAttendee($c);
                }

                // sendUpdates=all: Google envia e-mail de atualização para todos os convidados
                $evento->save('updateEvent', ['sendUpdates' => 'all']);
                $eventoExistente->update(['sincronizado_em' => now()]);
                return;
            } catch (\Throwable $e) {
                Log::warning("SincronizarGoogleCalendarJob: evento {$eventoExistente->google_event_id} não encontrado no Calendar, criando novo. Erro: {$e->getMessage()}");
            }
        }

        // Cria o evento base sem attendees para obter o objeto
        $evento = new Event();
        $evento->name          = $titulo;
        $evento->description   = $descricao;
        $evento->location      = $culto->local ?? '';
        $evento->startDateTime = $inicio;
        $evento->endDateTime   = $fim;

        foreach ($convidados as $c) {
            $evento->addAttendee($c);
        }

        // sendUpdates=all: Google envia convite por e-mail para cada participante
        $salvo = $evento->save('insertEvent', ['sendUpdates' => 'all']);

        GoogleCalendarEvento::updateOrCreate(
            ['culto_id' => $culto->id],
            [
                'google_event_id'  => $salvo->id,
                'calendar_id'      => config('google-calendar.calendar_id'),
                'html_link'        => $salvo->htmlLink ?? null,
                'sincronizado_em'  => now(),
            ]
        );
    }

    /**
     * Coleta e-mails dos usuários escalados no culto.
     *
     * @return array<array{email: string, name: string, comment: string}>
     */
    private function coletarConvidados(Culto $culto): array
    {
        return $culto->escalas
            ->filter(fn ($e) => ! empty($e->user?->email))
            ->map(fn ($e) => [
                'email'   => $e->user->email,
                'name'    => $e->user->name,
                'comment' => $e->funcao?->nome ?? '',
            ])
            ->unique('email')
            ->values()
            ->all();
    }

    private function excluirEvento(Culto $culto): void
    {
        $registro = $culto->googleCalendarEvento;

        if (! $registro?->google_event_id) {
            return;
        }

        try {
            $evento = Event::find($registro->google_event_id);
            // sendUpdates=all: Google notifica os convidados sobre o cancelamento
            $evento->delete($registro->google_event_id, ['sendUpdates' => 'all']);
        } catch (\Throwable $e) {
            Log::warning("SincronizarGoogleCalendarJob: não foi possível excluir evento {$registro->google_event_id}: {$e->getMessage()}");
        }

        $registro->delete();
    }

    private function montarDescricao(Culto $culto): string
    {
        $linhas = [];

        if ($culto->tema) {
            $linhas[] = "Tema: {$culto->tema}";
        }

        $escalados = $culto->escalas->map(fn ($e) => "{$e->funcao?->nome}: {$e->user?->name}")->filter()->values();
        if ($escalados->isNotEmpty()) {
            $linhas[] = '';
            $linhas[] = 'Equipe:';
            foreach ($escalados as $linha) {
                $linhas[] = "• {$linha}";
            }
        }

        if ($culto->observacoes) {
            $linhas[] = '';
            $linhas[] = "Observações: {$culto->observacoes}";
        }

        return implode("\n", $linhas);
    }

    private function credenciaisConfiguradas(): bool
    {
        $credFile = config('google-calendar.auth_profiles.service_account.credentials_json');

        return ! empty(config('google-calendar.calendar_id'))
            && file_exists($credFile);
    }
}
