@extends('layouts.portal')

@section('title', 'Status da Solicitação')

@section('content')
<div class="card center">
    <div class="card-body">

        @if($solicitacao->status == 'aprovado')
            <div class="icon bg-success">&#10003;</div>
            <h1 class="text-success">Acesso Liberado!</h1>
            <p class="muted">Seu acesso à rede Wi-Fi de visitantes da FFLCH-USP foi autorizado.</p>
            @if($solicitacao->user)
                <p class="muted">Aprovado por <strong>{{ $solicitacao->user->name }}</strong>.</p>
            @endif
            @if($solicitacao->mensagem())
                <div class="info"><small>Mensagem</small>{{ $solicitacao->mensagem() }}</div>
            @endif

            <div class="alert alert-info">
                <strong>Desligue e ligue o Wi-Fi</strong> do seu dispositivo para conectar na internet.
            </div>

        @elseif($solicitacao->status == 'rejeitado')
            <div class="icon bg-danger">&#10005;</div>
            <h1 class="text-danger">Solicitação Rejeitada</h1>
            @if($solicitacao->user)
                <p class="muted">Rejeitada por <strong>{{ $solicitacao->user->name }}</strong>.</p>
            @endif
            @if($solicitacao->mensagem())
                <div class="info"><small>Motivo</small>{{ $solicitacao->mensagem() }}</div>
            @endif
            <p class="muted small">Se discordar, peça uma segunda avaliação pelo link enviado para o seu e-mail.</p>

        @else
            <div class="icon bg-warning">&#8987;</div>

            @if($rejeicao = $solicitacao->ultimaRejeicao())
                <h1>Solicitação em Análise</h1>
                <div class="alert alert-info">
                    Sua solicitação foi rejeitada por <strong>{{ $rejeicao['quem'] }}</strong>
                    (motivo: <i>{{ $rejeicao['motivo'] }}</i>) e voltou para análise.
                </div>
            @else
                <h1>Solicitação em Análise</h1>
                <p class="muted">Recebemos sua solicitação de acesso à rede Wi-Fi de visitantes da FFLCH-USP e ela está em análise.</p>
            @endif

            <p class="muted">
                <strong>Qualquer professor ou funcionário da FFLCH</strong> pode aprovar seu acesso.
                Procure a pessoa responsável pela sua visita:
            </p>
            <ul class="lista">
                <li><strong>Professor visitante:</strong> procure o professor da FFLCH com quem está trabalhando.</li>
                <li><strong>Participante de evento:</strong> procure o organizador do evento.</li>
                <li><strong>Funcionário terceirizado (limpeza/vigilância):</strong> procure o encarregado responsável.</li>
            </ul>
        @endif

        <div class="info">
            <small>Dispositivo (MAC)</small>
            <strong>{{ $solicitacao->visitante->mac }}</strong>
        </div>

        @if($solicitacao->status == 'pendente')
            <div>
                <a href="/solicitar/{{ $solicitacao->id }}" class="btn btn-outline">&#8635; Atualizar</a>
            </div>
        @endif
    </div>
</div>
@endsection

