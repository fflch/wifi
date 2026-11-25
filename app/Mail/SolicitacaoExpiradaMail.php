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
 * Ninguém avaliou em 1 hora: avisa o visitante e manda o link para reabrir com novo motivo.
 */
class SolicitacaoExpiradaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Solicitacao $solicitacao,
        public string $linkReavaliacao
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wi-Fi FFLCH: sua solicitação não foi aprovada',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitacoes.expirada',
        );
    }
}
