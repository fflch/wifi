{{-- Aprovar: autorizador, quando ainda não está aprovada. Rejeitar: regra no gate 'rejeitar'. --}}
@if($solicitacao->status != 'aprovado')
    @can('autorizador')
        <form method="POST" action="/aprovacoes/{{ $solicitacao->id }}/aprovar" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Liberar o acesso de {{ $solicitacao->visitante->nome }}?');">Aprovar</button>
        </form>
    @endcan
@endif
@can('rejeitar', $solicitacao)
    <form method="POST" action="/aprovacoes/{{ $solicitacao->id }}/rejeitar" class="d-inline">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Tem certeza que deseja rejeitar?');">Rejeitar</button>
    </form>
@endcan
{{-- Excluir: somente admin --}}
@can('admin')
    <form method="POST" action="/aprovacoes/{{ $solicitacao->id }}" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Excluir definitivamente esta solicitação? Se estiver aprovada, o acesso será cortado.');">Excluir</button>
    </form>
@endcan
