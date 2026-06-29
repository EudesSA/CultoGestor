<?php

namespace App\Notifications;

use App\Models\Musica;
use App\Notifications\Concerns\ComWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class CantorAgendadoNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ComWebPush;

    public function __construct(public readonly Musica $musica) {}

    public function via(object $notifiable): array
    {
        return $this->canaisComPush($notifiable);
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $tipo = $this->musica->culto?->tipo?->nome ?? 'Culto';
        $data = $this->musica->culto?->data?->format('d/m/Y') ?? '';

        return (new WebPushMessage)
            ->title('Música aprovada! 🎵')
            ->body("\"{$this->musica->nome}\" foi aprovada" . ($data ? " para o {$tipo} de {$data}" : '') . '.')
            ->icon('/icons/icon-192.png')
            ->data(['url' => route('cantor.portal', $notifiable->cantor?->token_portal ?? '')]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $culto  = $this->musica->culto;
        $data   = $culto?->data?->format('d/m/Y') ?? '—';
        $hora   = $culto?->hora_inicio ? substr($culto->hora_inicio, 0, 5) : '';
        $tipo   = $culto?->tipo?->nome ?? 'Culto';
        $musica = $this->musica->nome;

        return (new MailMessage)
            ->subject("Música aprovada — {$musica}")
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line("Sua música **{$musica}** foi aprovada para o {$tipo} de **{$data}" . ($hora ? " às {$hora}" : '') . "**.")
            ->when($this->musica->tom, fn ($mail) => $mail->line("Tom: {$this->musica->tom}"))
            ->when($this->musica->artista, fn ($mail) => $mail->line("Artista: {$this->musica->artista}"))
            ->line('Prepare-se para o culto e envie os arquivos necessários pelo Portal do Cantor.')
            ->salutation('CultoGestor · ' . config('app.name'));
    }
}
