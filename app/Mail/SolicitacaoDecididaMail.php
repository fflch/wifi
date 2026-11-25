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
 * Avisa o visitante que a solicitação foi aprovada ou rejeitada, por quem e por quê.
 */
class SolicitacaoDecididaMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Solicitacao $solicitacao,
        public string $decisao,       // 'aprovada' ou 'rejeitada'
        public string $quem,          // nome de quem decidiu
        public ?string $mensagem,     // opcional ao aprovar, obrigatória ao rejeitar
        public bool $devolvida = false,      // rejeitada e devolvida para outra pessoa avaliar
        public ?string $linkReavaliacao = null // link assinado para pedir segunda avaliação
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wi-Fi FFLCH: sua solicitação foi '.$this->decisao,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitacoes.decisao',
        );
    }
}
