@extends('laravel-usp-theme::master')

@section('title', 'Aprovações Pendentes')

@section('content')
<h3>Aprovações Pendentes</h3>
<p class="text-muted">Visitantes que solicitaram acesso à rede Wi-Fi e aguardam autorização.</p>

@include('aprovacoes._busca')

<div class="row">
    @forelse($solicitacoes as $solicitacao)
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card h-100 border-warning shadow-sm">
                <div class="card-header bg-warning d-flex justify-content-between">
                    <strong>Nova solicitação</strong>
                    <span title="{{ $solicitacao->created_at->format('d/m/Y H:i') }}">{{ $solicitacao->created_at->diffForHumans() }}</span>
                </div>

                <div class="card-body">
                    <h4 class="card-title mb-1">
                        <a href="/aprovacoes/{{ $solicitacao->id }}" class="text-dark">{{ $solicitacao->visitante->nome }}</a>
                    </h4>
                    <p class="text-muted mb-3">{{ $solicitacao->visitante->email }}</p>

                    <p class="mb-3" style="font-size: 1.1rem;">
                        <i class="fas fa-quote-left text-muted"></i> {{ $solicitacao->motivo }}
                    </p>

                    <small class="text-muted">
                        Documento: {{ $solicitacao->visitante->documento_mascarado }} <br>
                        @if($solicitacao->visitante->telefone)
                            Telefone: {{ $solicitacao->visitante->telefone }} <br>
                        @endif
                        MAC: <code>{{ $solicitacao->visitante->mac }}</code>
                    </small>
                </div>

                <div class="card-footer bg-white">
                    <div class="row">
                        <div class="col-6">
                            <form method="POST" action="/aprovacoes/{{ $solicitacao->id }}/aprovar">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-lg btn-block" onclick="return confirm('Liberar o acesso de {{ $solicitacao->visitante->nome }}?');">
                                    <i class="fas fa-check"></i> Aprovar
                                </button>
                            </form>
                        </div>
                        <div class="col-6">
                            <form method="POST" action="/aprovacoes/{{ $solicitacao->id }}/rejeitar">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger btn-lg btn-block" onclick="return confirm('Tem certeza que deseja rejeitar {{ $solicitacao->visitante->nome }}?');">
                                    <i class="fas fa-times"></i> Rejeitar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-success text-center py-4">
                <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                Nenhuma solicitação pendente no momento.
            </div>
        </div>
    @endforelse
</div>

{{ $solicitacoes->links() }}
@endsection
