<?php

namespace App\Http\Controllers;

use App\Http\Requests\SolicitacaoRequest;
use App\Models\Solicitacao;
use App\Models\Visitante;
use Illuminate\Http\Request;

/**
 * Telas do visitante (captive portal). Não exige login.
 */
class SolicitacaoController extends Controller
{
    /**
     * A controladora Wi-Fi redireciona o visitante para cá com
     * ?client_mac=...&nbiIP=...&ssid=...
     * (a Ruckus manda "nbiIP"; aceitamos também "NbiIP")
     */
    public function create(Request $request)
    {
        if (! filter_var($request->client_mac, FILTER_VALIDATE_MAC)) {
            abort(403, 'Acesso negado. Parâmetros obrigatórios ausentes ou inválidos.');
        }
        if ($request->ssid != config('wifi.ssid')) {
            abort(403, 'SSID inválido.');
        }
        $nbiIp = $request->input('nbiIP', $request->input('NbiIP'));
        if ($nbiIp != config('wifi.nbi_ip')) {
            abort(403, 'IP da controladora inválido.');
        }

        $mac = Visitante::normalizaMac($request->client_mac);
        $visitante = Visitante::where('mac', $mac)->first();

        // Visitante que já pediu acesso vai direto para a tela de status
        if ($visitante) {
            $solicitacao = $visitante->solicitacoes()->latest()->first();

            if ($solicitacao) {
                // Rejeitado que volta ao portal: a solicitação é reaberta para outro autorizador
                if ($solicitacao->status == 'rejeitado') {
                    $rejeitadoPor = $solicitacao->user?->name;
                    $solicitacao->user_id = null;
                    $solicitacao->save();
                    $solicitacao->setStatus('pendente', 'Reaberta pelo visitante');

                    return redirect("/solicitar/{$solicitacao->id}")->with('reaberta', $rejeitadoPor ?? 'um servidor');
                }

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
}
