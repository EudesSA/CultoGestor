<?php

namespace App\Notifications\Concerns;

use NotificationChannels\WebPush\WebPushChannel;

/**
 * Acrescenta o canal Web Push aos canais da notificação quando o destinatário
 * tem alguma inscrição de push (PWA instalado e notificações ativadas).
 * Sem inscrição, cai apenas no e-mail — sem erro.
 */
trait ComWebPush
{
    protected function canaisComPush(object $notifiable, array $base = ['mail']): array
    {
        if (method_exists($notifiable, 'pushSubscriptions') && $notifiable->pushSubscriptions()->exists()) {
            $base[] = WebPushChannel::class;
        }

        return $base;
    }
}
