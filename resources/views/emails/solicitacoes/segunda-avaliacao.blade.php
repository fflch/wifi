<p>Olá, {{ $solicitacao->user?->name }}.</p>

<p>
    <b>{{ $solicitacao->visitante->nome }}</b> ({{ $solicitacao->visitante->email }})
    pediu uma segunda avaliação da solicitação de acesso à rede Wi-Fi de visitantes que você rejeitou.
</p>

@if($rejeicao = $solicitacao->ultimaRejeicao())
    <p>Seu motivo da rejeição:<br><i>{{ $rejeicao['motivo'] }}</i></p>
@endif

<p>Justificativa do visitante:<br><i>{{ $justificativa }}</i></p>

<p>
    A solicitação voltou para a lista de pendentes e pode ser avaliada por você ou por outro servidor:<br>
    <a href="{{ url('/aprovacoes/'.$solicitacao->id) }}">{{ url('/aprovacoes/'.$solicitacao->id) }}</a>
</p>

@include('emails.solicitacoes._vinculo')

<p>Faculdade de Filosofia, Letras e Ciências Humanas - USP</p>
