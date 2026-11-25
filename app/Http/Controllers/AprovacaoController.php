<?php

namespace App\Http\Controllers;

use App\Mail\SolicitacaoDecididaMail;
use App\Models\Solicitacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

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

    /**
     * Aprova. A mensagem ao visitante é opcional.
     */
    public function aprovar(Request $request, Solicitacao $solicitacao)
    {
        Gate::authorize('autorizador');

        if ($solicitacao->status == 'aprovado') {
            return back()->with('alert-info', 'Esta solicitação já está aprovada.');
        }

        $request->validate([
            'mensagem' => 'nullable|max:1000',
        ]);

        $solicitacao->user_id = auth()->user()->id;
        $solicitacao->save();
        $solicitacao->setStatus('aprovado', $request->mensagem);

        Mail::to($solicitacao->visitante->email)->queue(
            new SolicitacaoDecididaMail($solicitacao, 'aprovada', auth()->user()->name, $request->mensagem)
        );

        return back()->with('alert-success', 'Acesso liberado para '.$solicitacao->visitante->nome.'.');
    }

    /**
     * Rejeita (o motivo é obrigatório). Regra de quem pode no gate 'rejeitar'.
     * Com "devolver", a solicitação volta para /aprovacoes para outra pessoa avaliar.
     */
    public function rejeitar(Request $request, Solicitacao $solicitacao)
    {
        Gate::authorize('rejeitar', $solicitacao);

        $request->validate([
            'mensagem' => 'required|min:5|max:1000',
        ], [
            'mensagem.required' => 'Informe o motivo da rejeição.',
            'mensagem.min' => 'O motivo da rejeição deve ter pelo menos 5 caracteres.',
        ]);

        $solicitacao->user_id = auth()->user()->id;
        $solicitacao->save();
        $solicitacao->setStatus('rejeitado', $request->mensagem);

        if ($request->devolver) {
            $solicitacao->setStatus('pendente', 'Devolvida para outra pessoa avaliar');
        }

        // Sem devolver: o visitante recebe um link assinado para pedir segunda avaliação,
        // preso a esta rejeição (parâmetro "rejeicao") e válido por 7 dias
        $link = $request->devolver ? null : $solicitacao->linkReavaliacao();

        Mail::to($solicitacao->visitante->email)->queue(
            new SolicitacaoDecididaMail($solicitacao, 'rejeitada', auth()->user()->name, $request->mensagem, (bool) $request->devolver, $link)
        );

        if ($request->devolver) {
            return back()->with('alert-info', 'Solicitação de '.$solicitacao->visitante->nome.' rejeitada e devolvida para outra pessoa avaliar.');
        }

        return back()->with('alert-info', 'Solicitação de '.$solicitacao->visitante->nome.' rejeitada.');
    }

    /**
     * Exclui a solicitação. Somente admin (SENHAUNICA_ADMINS).
     * Se estava aprovada, o MAC sai da radcheck. A exclusão fica na auditoria.
     */
    public function destroy(Solicitacao $solicitacao)
    {
        Gate::authorize('admin');

        $visitante = $solicitacao->visitante;
        $nome = $visitante->nome;

        $solicitacao->statuses()->delete();
        $solicitacao->delete();
        $visitante->sincronizarRadius();

        return redirect('/aprovacoes/todas')->with('alert-info', 'Solicitação de '.$nome.' excluída.');
    }

    private function busca($query, $search)
    {
        $query->where('nome', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('documento', 'like', "%{$search}%")
            ->orWhere('mac', 'like', "%{$search}%");
    }
}
