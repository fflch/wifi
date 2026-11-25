<?php

namespace App\Mail;

use App\Models\Solicitacao;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Avisa quem rejeitou que o visitante pediu uma segunda avaliação.
 */
class SegundaAvaliacaoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Solicitacao $solicitacao,
        public string $justificativa
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wi-Fi FFLCH: '.$this->solicitacao->visitante->nome.' pediu uma segunda avaliação',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitacoes.segunda-avaliacao',
        );
    }
}
