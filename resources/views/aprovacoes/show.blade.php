@extends('laravel-usp-theme::master')

@section('title', 'Solicitação')

@section('content')
<h3>
    {{ $solicitacao->visitante->nome }}
    <span class="badge badge-{{ $solicitacao->cor() }}">{{ $solicitacao->status }}</span>
</h3>

<p>
    E-mail: {{ $solicitacao->visitante->email }} <br>
    Documento: {{ $solicitacao->visitante->documento }} <br>
    Telefone: {{ $solicitacao->visitante->telefone ?? '-' }} <br>
    MAC: <code>{{ $solicitacao->visitante->mac }}</code> <br>
    Motivo: {{ $solicitacao->motivo }} <br>
    Solicitado em {{ $solicitacao->created_at->format('d/m/Y H:i') }} <br>
    @if($solicitacao->user)
        <b>{{ ucfirst($solicitacao->status) }} por {{ $solicitacao->user->name }}</b>
        em {{ $solicitacao->latestStatus()->created_at->format('d/m/Y H:i') }}
    @endif
</p>

@include('aprovacoes._erros')
@if(Gate::allows('rejeitar', $solicitacao) || ($solicitacao->status != 'aprovado' && Gate::allows('autorizador')))
    <div class="card mb-3" style="max-width: 600px;">
        <div class="card-body">
            @include('aprovacoes._decisao')
        </div>
    </div>
@endif
@include('aprovacoes._excluir')

<h5 class="mt-4">Histórico</h5>
<p class="text-muted small">Registrado pela auditoria (owen-it/laravel-auditing).</p>
<table class="table table-sm">
    <thead class="thead-light">
        <tr><th>Data</th><th>Ação</th><th>Quem</th><th>Detalhe</th><th>IP</th></tr>
    </thead>
    <tbody>
        @forelse($solicitacao->historico() as $item)
            <tr>
                <td class="text-nowrap">{{ $item['data']->format('d/m/Y H:i:s') }}</td>
                <td><span class="badge badge-{{ $item['cor'] }}">{{ $item['acao'] }}</span></td>
                <td>{{ $item['quem'] }}</td>
                <td>{{ $item['detalhe'] }}</td>
                <td><small class="text-muted">{{ $item['ip'] }}</small></td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-muted">Nenhum registro na auditoria.</td></tr>
        @endforelse
    </tbody>
</table>

<a href="{{ url()->previous() }}">Voltar</a>
@endsection
