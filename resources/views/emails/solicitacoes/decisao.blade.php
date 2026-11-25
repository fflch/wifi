<p>Olá, {{ $solicitacao->visitante->nome }}.</p>

<p>
    Sua solicitação de acesso à rede Wi-Fi de visitantes da FFLCH-USP
    (dispositivo <code>{{ $solicitacao->visitante->mac }}</code>)
    foi <b>{{ $decisao }}</b> por <b>{{ $quem }}</b>.
</p>

@if($mensagem)
    <p>
        {{ $decisao == 'rejeitada' ? 'Motivo' : 'Mensagem' }}:<br>
        <i>{{ $mensagem }}</i>
    </p>
@endif

@if($decisao == 'aprovada')
    <p>Seu dispositivo já pode navegar. Se ainda não conseguir, desconecte e conecte de novo à rede Wi-Fi.</p>
@elseif($devolvida)
    <p>Sua solicitação foi encaminhada para outra pessoa avaliar. Você receberá um novo e-mail quando houver uma decisão.</p>
@elseif($linkReavaliacao)
    <p>
        Se você discorda, pode pedir uma segunda avaliação por este link
        (pessoal, de uso único e válido por 7 dias; não o compartilhe):<br>
        <a href="{{ $linkReavaliacao }}">Pedir segunda avaliação</a>
    </p>
@endif

<p>Faculdade de Filosofia, Letras e Ciências Humanas - USP</p>
