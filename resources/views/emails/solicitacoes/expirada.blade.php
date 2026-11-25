<p>Olá, {{ $solicitacao->visitante->nome }}.</p>

<p>
    Nenhum docente ou funcionário da FFLCH aprovou sua solicitação de acesso à rede Wi-Fi de visitantes
    (dispositivo <code>{{ $solicitacao->visitante->mac }}</code>) dentro de 1 hora, por isso ela foi encerrada.
</p>

<p>O motivo informado foi:<br><i>{{ $solicitacao->motivo }}</i></p>

<p>
    Você pode reabrir a solicitação descrevendo <b>de forma mais específica o motivo da sua visita à FFLCH</b>:
    com quem você vai se encontrar, em qual departamento, evento ou atividade. Isso ajuda um docente ou
    funcionário a reconhecer e aprovar o seu pedido. Ao reabrir, ela volta para avaliação por mais 1 hora.
</p>

<p>
    <a href="{{ $linkReavaliacao }}">Reabrir solicitação com novo motivo</a><br>
    <small>Link pessoal, de uso único e válido por 7 dias; não o compartilhe.
    Você também pode reabrir conectando-se à rede Wi-Fi de visitantes da FFLCH e abrindo o navegador.</small>
</p>

@include('emails.solicitacoes._vinculo')

<p>Faculdade de Filosofia, Letras e Ciências Humanas - USP</p>
