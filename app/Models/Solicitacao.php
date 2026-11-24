<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\ModelStatus\HasStatuses;

/**
 * Status (spatie/laravel-model-status): pendente, aprovado, rejeitado.
 */
class Solicitacao extends Model implements Auditable
{
    use HasFactory;
    use HasStatuses;
    use HasUlids;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'solicitacoes';

    // Quem aprovou/rejeitou já fica registrado no audit de status (ver AppServiceProvider)
    protected $auditExclude = ['user_id'];

    public function visitante()
    {
        return $this->belongsTo(Visitante::class);
    }

    /**
     * Autorizador que aprovou ou rejeitou por último
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Histórico da solicitação, do mais antigo ao mais recente, montado a partir da auditoria.
     * Cada item: data, quem, acao, detalhe, ip
     */
    public function historico()
    {
        $historico = [];

        foreach ($this->audits()->with('user')->oldest('id')->get() as $audit) {
            $antes = $audit->old_values['status'] ?? null;
            $depois = $audit->new_values['status'] ?? null;
            $detalhe = $audit->new_values['observacao'] ?? null;

            if ($audit->event == 'created') {
                $acao = 'Solicitação criada';
                $detalhe = 'Motivo: '.($audit->new_values['motivo'] ?? '');
            } elseif ($audit->event == 'status' && $depois == 'pendente') {
                $acao = $antes == 'rejeitado' ? 'Reaberta pelo visitante' : 'Enviada para análise';
            } elseif ($audit->event == 'status' && $depois == 'aprovado') {
                $acao = 'Aprovada';
            } elseif ($audit->event == 'status' && $depois == 'rejeitado') {
                $acao = $antes == 'aprovado' ? 'Aprovação revogada' : 'Rejeitada';
            } else {
                $acao = 'Dados alterados';
                $detalhe = implode(', ', array_keys($audit->new_values));
            }

            if ($detalhe == $acao) {
                $detalhe = null;
            }

            $historico[] = [
                'data' => $audit->created_at,
                'quem' => $audit->user?->name ?? 'Visitante',
                'acao' => $acao,
                'cor' => $depois == 'aprovado' ? 'success' : ($depois == 'rejeitado' ? 'danger' : 'secondary'),
                'detalhe' => $detalhe,
                'ip' => $audit->ip_address,
            ];
        }

        return $historico;
    }

    /**
     * Classe de cor do Bootstrap para o status atual
     */
    public function cor()
    {
        if ($this->status == 'aprovado') {
            return 'success';
        }
        if ($this->status == 'rejeitado') {
            return 'danger';
        }

        return 'warning';
    }
}
