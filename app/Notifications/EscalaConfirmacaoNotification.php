<?php

namespace App\Notifications;

use App\Models\Escala;
use App\Notifications\Concerns\ComWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class EscalaConfirmacaoNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ComWebPush;

    public function __construct(public readonly Escala $escala) {}

    public function via(object $notifiable): array
    {
        return $this->canaisComPush($notifiable);
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $culto  = $this->escala->culto;
        $funcao = $this->escala->funcao?->nome ?? 'voluntário';
        $data   = $culto?->data?->format('d/m/Y') ?? '';
        $tipo   = $culto?->tipo?->nome ?? 'Culto';

        return (new WebPushMessage)
            ->title('Você foi escalado 🙌')
            ->body("{$funcao} no {$tipo}" . ($data ? " de {$data}" : '') . '. Toque para confirmar.')
            ->icon('/icons/icon-192.png')
            ->data(['url' => route('escala.confirmacao', $this->escala->token_confirmacao)]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $culto  = $this->escala->culto;
        $funcao = $this->escala->funcao?->nome ?? 'sua função';
        $data   = $culto?->data?->format('d/m/Y') ?? '—';
        $hora   = $culto?->hora_inicio ? substr($culto->hora_inicio, 0, 5) : '';
        $tipo   = $culto?->tipo?->nome ?? 'Culto';
        $url    = route('escala.confirmacao', $this->escala->token_confirmacao);

        return (new MailMessage)
            ->subject("Você foi escalado — {$tipo} em {$data}")
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line("Você foi escalado como **{$funcao}** para o {$tipo} de **{$data}" . ($hora ? " às {$hora}" : '') . "**.")
            ->when($culto?->tema, fn ($mail) => $mail->line("Tema: *{$culto->tema}*"))
            ->when($culto?->local, fn ($mail) => $mail->line("Local: {$culto->local}"))
            ->action('Confirmar presença', $url)
            ->line('Caso não possa comparecer, utilize o link acima para registrar sua recusa.')
            ->line('📅 [Adicionar este culto ao meu Google Agenda](' . \App\Support\GoogleCalendar::linkCulto($culto, 'Função: ' . $funcao) . ')')
            ->salutation('CultoGestor · ' . config('app.name'));
    }
}
