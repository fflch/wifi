@extends('laravel-usp-theme::master')

@section('title', 'Aprovações Pendentes')

@section('content')
<h3>Aprovações Pendentes</h3>
<p class="text-muted">Visitantes que solicitaram acesso à rede Wi-Fi e aguardam autorização.</p>

@include('aprovacoes._erros')
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

                    @if($rejeicao = $solicitacao->ultimaRejeicao())
                        <div class="alert alert-secondary small py-2">
                            Já rejeitada por <b>{{ $rejeicao['quem'] }}</b> em {{ $rejeicao['data']->format('d/m/Y H:i') }}:
                            <i>{{ $rejeicao['motivo'] }}</i>
                        </div>
                    @endif
                    @if($pedido = $solicitacao->pedidoSegundaAvaliacao())
                        <div class="alert alert-info small py-2">
                            <b>Segunda avaliação pedida pelo visitante:</b> <i>{{ $pedido }}</i>
                        </div>
                    @endif

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
                    @include('aprovacoes._decisao')
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
