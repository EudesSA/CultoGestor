<?php

namespace App\Notifications;

use App\Models\Escala;
use App\Notifications\Concerns\ComWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

/**
 * Lembrete enviado automaticamente 3 dias antes do culto (NotificarEscalaJob)
 * para quem ainda não confirmou presença. Reaproveita o link tokenizado de
 * confirmação da EscalaConfirmacaoNotification.
 */
class EscalaLembreteNotification extends Notification implements ShouldQueue
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
        $culto = $this->escala->culto;
        $data  = $culto?->data?->format('d/m/Y') ?? '';
        $tipo  = $culto?->tipo?->nome ?? 'Culto';

        return (new WebPushMessage)
            ->title('Faltam 3 dias ⏰')
            ->body("Confirme sua presença no {$tipo}" . ($data ? " de {$data}" : '') . '.')
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
            ->subject("Lembrete: faltam 3 dias — {$tipo} em {$data}")
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line("Faltam **3 dias** para o {$tipo} de **{$data}" . ($hora ? " às {$hora}" : '') . "**, e você ainda não confirmou sua presença como **{$funcao}**.")
            ->when($culto?->tema, fn ($mail) => $mail->line("Tema: *{$culto->tema}*"))
            ->when($culto?->local, fn ($mail) => $mail->line("Local: {$culto->local}"))
            ->action('Confirmar presença', $url)
            ->line('Caso não possa comparecer, utilize o link acima para registrar sua recusa.')
            ->salutation('CultoGestor · ' . config('app.name'));
    }
}
