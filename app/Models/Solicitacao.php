<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
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

    // Pendente há mais que isso é rejeitada automaticamente (app:rejeita-pendentes)
    const MINUTOS_PARA_AVALIAR = 60;

    const REJEICAO_AUTOMATICA = 'Nenhum docente ou funcionário avaliou a solicitação em 1 hora';

    const REABERTA_NOVO_MOTIVO = 'Reaberta pelo visitante com novo motivo';

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
                if ($detalhe == self::REABERTA_NOVO_MOTIVO) {
                    $acao = 'Reaberta com novo motivo';
                    $detalhe = null;
                } elseif ($antes == 'rejeitado' && $audit->user) {
                    $acao = 'Devolvida para outra avaliação';
                    $detalhe = null;
                } elseif ($antes == 'rejeitado') {
                    $acao = 'Segunda avaliação pedida';   // pelo visitante, via link do e-mail
                } else {
                    $acao = 'Enviada para análise';
                    $detalhe = null;
                }
            } elseif ($audit->event == 'status' && $depois == 'aprovado') {
                $acao = 'Aprovada';
            } elseif ($audit->event == 'status' && $depois == 'rejeitado' && $detalhe == self::REJEICAO_AUTOMATICA) {
                $acao = 'Rejeitada automaticamente';
            } elseif ($audit->event == 'status' && $depois == 'rejeitado') {
                $acao = $antes == 'aprovado' ? 'Aprovação revogada' : 'Rejeitada';
            } elseif (isset($audit->new_values['motivo'])) {
                $acao = 'Motivo alterado';
                $detalhe = 'Novo motivo: '.$audit->new_values['motivo'];
            } else {
                $acao = 'Dados alterados';
                $detalhe = implode(', ', array_keys($audit->new_values));
            }

            if ($detalhe == $acao) {
                $detalhe = null;
            }

            $historico[] = [
                'data' => $audit->created_at,
                'quem' => $audit->user?->name ?? ($acao == 'Rejeitada automaticamente' ? 'Sistema' : 'Visitante'),
                'acao' => $acao,
                'cor' => $depois == 'aprovado' ? 'success' : ($depois == 'rejeitado' ? 'danger' : 'secondary'),
                'detalhe' => $detalhe,
                'ip' => $audit->ip_address,
            ];
        }

        return $historico;
    }

    /**
     * Última rejeição (quem, motivo e data), para avisar quem vai reavaliar.
     */
    public function ultimaRejeicao()
    {
        foreach ($this->historico() as $item) {
            if (in_array($item['acao'], ['Rejeitada', 'Aprovação revogada', 'Rejeitada automaticamente'])) {
                $ultima = [
                    'quem' => $item['quem'],
                    'motivo' => $item['detalhe'],
                    'data' => $item['data'],
                    'automatica' => $item['acao'] == 'Rejeitada automaticamente',
                ];
            }
        }

        return $ultima ?? null;
    }

    /**
     * Justificativa do visitante, se a solicitação está pendente por pedido de segunda avaliação
     */
    public function pedidoSegundaAvaliacao()
    {
        $ultimo = collect($this->historico())->last();

        return $ultimo && $ultimo['acao'] == 'Segunda avaliação pedida' ? $ultimo['detalhe'] : null;
    }

    /**
     * Rejeitada pelo sistema por ninguém ter avaliado a tempo
     */
    public function rejeitadaAutomaticamente()
    {
        return $this->status == 'rejeitado' && $this->mensagem() == self::REJEICAO_AUTOMATICA;
    }

    /**
     * Link assinado (7 dias, uso único) para o visitante pedir segunda avaliação
     * ou reabrir com novo motivo. Preso à rejeição atual pelo parâmetro "rejeicao".
     */
    public function linkReavaliacao($absoluto = true)
    {
        $link = URL::temporarySignedRoute('reavaliacao', now()->addDays(7), [
            'solicitacao' => $this->id,
            // Consulta o banco: a relação 'statuses' pode estar carregada de antes da rejeição
            'rejeicao' => $this->statuses()->first()->id,
        ], absolute: false);

        // Absoluto para o e-mail; relativo para redirecionar no portal (mantém o host da requisição)
        return $absoluto ? url($link) : $link;
    }

    /**
     * Mensagem da última decisão (motivo da rejeição ou mensagem da aprovação)
     */
    public function mensagem()
    {
        return $this->latestStatus()?->reason;
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
