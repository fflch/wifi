<?php

namespace App\Console\Commands;

use App\Mail\SolicitacaoExpiradaMail;
use App\Models\Solicitacao;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Rejeita automaticamente as solicitações pendentes há mais de 1 hora.
 * Ficam sem autorizador (user_id nulo) e só aparecem na lista dos admins.
 * O visitante recebe um link para reabrir com um motivo mais específico,
 * o que devolve a solicitação para a fila por mais 1 hora.
 * Agendado a cada minuto em routes/console.php.
 */
class RejeitaPendentes extends Command
{
    protected $signature = 'app:rejeita-pendentes';

    protected $description = 'Rejeita automaticamente as solicitações pendentes há mais de 1 hora';

    public function handle()
    {
        // Comandos não são auditados por padrão (audit.console); aqui queremos o registro no histórico
        config(['audit.console' => true]);

        $limite = now()->subMinutes(Solicitacao::MINUTOS_PARA_AVALIAR);
        $total = 0;

        foreach (Solicitacao::currentStatus('pendente')->with('statuses', 'visitante')->get() as $solicitacao) {
            // Conta a partir de quando ficou pendente (criação, devolução ou reabertura)
            if ($solicitacao->latestStatus()->created_at > $limite) {
                continue;
            }

            $solicitacao->user_id = null;
            $solicitacao->save();
            $solicitacao->setStatus('rejeitado', Solicitacao::REJEICAO_AUTOMATICA);

            Mail::to($solicitacao->visitante->email)->queue(
                new SolicitacaoExpiradaMail($solicitacao, $solicitacao->linkReavaliacao())
            );

            $total++;
        }

        $this->info("{$total} solicitação(ões) rejeitada(s) automaticamente.");
    }
}
