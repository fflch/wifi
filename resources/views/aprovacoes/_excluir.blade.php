{{-- Excluir: somente admin --}}
@can('admin')
    <form method="POST" action="/aprovacoes/{{ $solicitacao->id }}" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Excluir definitivamente esta solicitação? Se estiver aprovada, o acesso será cortado.');">Excluir</button>
    </form>
@endcan
