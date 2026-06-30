<?php

/**
 * CultoGestor — E-mail disparado quando o formulário de contato do site é enviado.
 *
 * @author  Eudes S. Aguiar — ProezaTech — www.proezatech.com
 * @link     https://www.proezatech.com
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContatoRecebido extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nomeRemetente,
        public string $emailRemetente,
        public string $mensagem,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Novo contato pelo site — CultoGestor',
            // Responder direto ao visitante que preencheu o formulário.
            replyTo: [new Address($this->emailRemetente, $this->nomeRemetente)],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contato',
        );
    }
}
