<?php

namespace App\Notifications;

use App\Models\EnsaioParticipante;
use App\Notifications\Concerns\ComWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class EnsaioConvocacaoNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use ComWebPush;

    public function __construct(public readonly EnsaioParticipante $participante) {}

    public function via(object $notifiable): array
    {
        return $this->canaisComPush($notifiable);
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $ensaio = $this->participante->ensaio;
        $quando = $ensaio?->data_hora?->format('d/m/Y \à\s H:i') ?? '';

        return (new WebPushMessage)
            ->title('Convocação para ensaio 🎼')
            ->body('Ensaio' . ($quando ? " em {$quando}" : '') . ($ensaio?->local ? " no {$ensaio->local}" : '') . '. Toque para confirmar.')
            ->icon('/icons/icon-192.png')
            ->data(['url' => route('ensaio.confirmacao', $this->participante->token_confirmacao)]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ensaio = $this->participante->ensaio;
        $quando = $ensaio?->data_hora?->format('d/m/Y \à\s H:i') ?? '—';
        $local  = $ensaio?->local;
        $url    = route('ensaio.confirmacao', $this->participante->token_confirmacao);

        $mail = (new MailMessage)
            ->subject("Convocação para ensaio — {$quando}")
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line("Você foi convocado para um ensaio em **{$quando}**.")
            ->when($local, fn ($m) => $m->line("Local: {$local}"))
            ->when($ensaio?->culto, fn ($m) => $m->line('Prepara para o culto de ' . $ensaio->culto->data?->format('d/m/Y') . '.'));

        if ($ensaio && $ensaio->musicas->isNotEmpty()) {
            $mail->line('**Repertório:** ' . $ensaio->musicas->pluck('nome')->implode(', '));
        }

        return $mail
            ->action('Confirmar presença', $url)
            ->line('Caso não possa comparecer, use o link acima para avisar.')
            ->when($ensaio, fn ($m) => $m->line('📅 [Adicionar este ensaio ao meu Google Agenda](' . \App\Support\GoogleCalendar::linkEnsaio($ensaio) . ')'))
            ->salutation('CultoGestor · ' . config('app.name'));
    }
}
