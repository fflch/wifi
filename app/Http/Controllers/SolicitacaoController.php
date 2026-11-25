<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolicitacaoRequest;
use App\Mail\SegundaAvaliacaoMail;
use App\Models\Solicitacao;
use App\Models\Visitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Telas do visitante (captive portal). Não exige login.
 */
class SolicitacaoController extends Controller
{
    /**
     * A controladora Wi-Fi redireciona o visitante para cá com
     * ?client_mac=...&nbiIP=...&ssid=...
     * (a Ruckus manda "nbiIP"; o nome é aceito sem diferenciar maiúsculas)
     */
    public function create(Request $request)
    {
        if (! filter_var($request->client_mac, FILTER_VALIDATE_MAC)) {
            abort(403, 'Acesso negado. Parâmetros obrigatórios ausentes ou inválidos.');
        }
        if ($request->ssid != config('wifi.ssid')) {
            abort(403, 'SSID inválido.');
        }
        // Nome do parâmetro sem diferenciar maiúsculas (nbiIP, NbiIP, nbiip...)
        $nbiIp = collect($request->query())
            ->first(fn ($valor, $chave) => strcasecmp($chave, 'nbiIP') === 0);
        if ($nbiIp != config('wifi.nbi_ip')) {
            abort(403, 'IP da controladora inválido.');
        }

        $mac = Visitante::normalizaMac($request->client_mac);
        $visitante = Visitante::where('mac', $mac)->first();

        // Visitante que já pediu acesso vai direto para a tela de status
        if ($visitante) {
            $solicitacao = $visitante->solicitacoes()->latest()->first();

            // Se foi rejeitada, a segunda avaliação só pode ser pedida pelo link enviado por e-mail
            if ($solicitacao) {
                return redirect("/solicitar/{$solicitacao->id}");
            }
        }

        return view('solicitacoes.create', [
            'mac' => $mac,
        ]);
    }

    public function store(SolicitacaoRequest $request)
    {
        $visitante = Visitante::firstOrNew(['mac' => $request->mac]);

        // Já tem solicitação: não cria outra, mostra a existente
        $existente = $visitante->solicitacoes()->latest()->first();
        if ($existente) {
            return redirect("/solicitar/{$existente->id}");
        }

        $visitante->nome = $request->nome;
        $visitante->email = $request->email;
        $visitante->documento = $request->documento;
        $visitante->telefone = $request->telefone;
        $visitante->save();

        $solicitacao = new Solicitacao;
        $solicitacao->visitante_id = $visitante->id;
        $solicitacao->motivo = $request->motivo;
        $solicitacao->save();
        $solicitacao->setStatus('pendente');

        return redirect("/solicitar/{$solicitacao->id}");
    }

    /**
     * Página de acompanhamento. O ULID no endereço funciona como protocolo.
     */
    public function show(Solicitacao $solicitacao)
    {
        return view('solicitacoes.show', [
            'solicitacao' => $solicitacao,
        ]);
    }

    /**
     * Formulário de segunda avaliação. Só abre pelo link assinado enviado no e-mail de rejeição
     * (middleware 'signed'). O link vale para uma única rejeição: o parâmetro "rejeicao"
     * precisa ser o status atual; depois de usado (ou de outra decisão) deixa de valer.
     */
    public function reavaliacao(Request $request, Solicitacao $solicitacao)
    {
        return view('solicitacoes.reavaliacao', [
            'solicitacao' => $solicitacao,
            'valido' => $this->linkValido($request, $solicitacao),
        ]);
    }

    public function pedirReavaliacao(Request $request, Solicitacao $solicitacao)
    {
        if (! $this->linkValido($request, $solicitacao)) {
            return redirect("/solicitar/{$solicitacao->id}");
        }

        $request->validate([
            'justificativa' => 'required|min:10|max:1000',
        ], [
            'justificativa.required' => 'Explique por que você pede uma segunda avaliação.',
            'justificativa.min' => 'A justificativa deve ter pelo menos 10 caracteres.',
        ]);

        // Quem rejeitou continua em user_id e recebe o pedido por e-mail
        $solicitacao->setStatus('pendente', $request->justificativa);

        if ($solicitacao->user?->email) {
            Mail::to($solicitacao->user->email)->queue(new SegundaAvaliacaoMail($solicitacao, $request->justificativa));
        }

        return redirect("/solicitar/{$solicitacao->id}");
    }

    private function linkValido(Request $request, Solicitacao $solicitacao)
    {
        return $solicitacao->status == 'rejeitado'
            && $solicitacao->latestStatus()->id == $request->query('rejeicao');
    }
}
