<?php

namespace App\Http\Controllers;

use App\Models\Solicitacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Telas do autorizador: servidores e docentes (gate 'autorizador' no AppServiceProvider).
 */
class AprovacaoController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('autorizador');

        $query = Solicitacao::currentStatus('pendente')->with('visitante', 'statuses', 'user');
        if ($request->filled('search')) {
            $query->whereHas('visitante', fn ($q) => $this->busca($q, $request->search));
        }

        return view('aprovacoes.index', [
            'solicitacoes' => $query->latest()->paginate(12)->withQueryString(),
        ]);
    }

    /**
     * Solicitações que o usuário logado aprovou ou rejeitou
     */
    public function minhas(Request $request)
    {
        Gate::authorize('autorizador');

        $query = Solicitacao::where('user_id', auth()->user()->id)->with('visitante', 'statuses', 'user');
        if ($request->filled('search')) {
            $query->whereHas('visitante', fn ($q) => $this->busca($q, $request->search));
        }

        return view('aprovacoes.minhas', [
            'solicitacoes' => $query->latest()->paginate(15)->withQueryString(),
        ]);
    }

    /**
     * Admin (SENHAUNICA_ADMINS) vê todas as solicitações, de todos os servidores.
     * A busca também procura pelo nome de quem aprovou/rejeitou.
     */
    public function todas(Request $request)
    {
        Gate::authorize('admin');

        $query = Solicitacao::with('visitante', 'statuses', 'user');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('visitante', fn ($v) => $this->busca($v, $search))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        return view('aprovacoes.todas', [
            'solicitacoes' => $query->latest()->paginate(20)->withQueryString(),
        ]);
    }

    public function show(Solicitacao $solicitacao)
    {
        if (Gate::none(['autorizador', 'admin'])) {
            abort(403);
        }

        return view('aprovacoes.show', [
            'solicitacao' => $solicitacao,
        ]);
    }

    public function aprovar(Solicitacao $solicitacao)
    {
        Gate::authorize('autorizador');

        $solicitacao->user_id = auth()->user()->id;
        $solicitacao->save();
        $solicitacao->setStatus('aprovado');

        return back()->with('alert-success', 'Acesso liberado para '.$solicitacao->visitante->nome.'.');
    }

    /**
     * Rejeita uma pendente ou revoga uma aprovação (regra no gate 'rejeitar')
     */
    public function rejeitar(Solicitacao $solicitacao)
    {
        Gate::authorize('rejeitar', $solicitacao);

        $solicitacao->user_id = auth()->user()->id;
        $solicitacao->save();
        $solicitacao->setStatus('rejeitado');

        return back()->with('alert-info', 'Solicitação de '.$solicitacao->visitante->nome.' rejeitada.');
    }

    private function busca($query, $search)
    {
        $query->where('nome', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('documento', 'like', "%{$search}%")
            ->orWhere('mac', 'like', "%{$search}%");
    }
}
