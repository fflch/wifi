<table class="table table-hover">
    <thead class="thead-light">
        <tr>
            <th>Data</th>
            <th>Visitante</th>
            <th>MAC</th>
            <th>Documento</th>
            <th>Motivo</th>
            <th>Status</th>
            <th>Decidido por</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse($solicitacoes as $solicitacao)
            <tr>
                <td>{{ $solicitacao->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="/aprovacoes/{{ $solicitacao->id }}">{{ $solicitacao->visitante->nome }}</a><br>
                    <small class="text-muted">{{ $solicitacao->visitante->email }}</small>
                </td>
                <td><code>{{ $solicitacao->visitante->mac }}</code></td>
                <td>{{ $solicitacao->visitante->documento_mascarado }}</td>
                <td>{{ Str::limit($solicitacao->motivo, 80) }}</td>
                <td><span class="badge badge-{{ $solicitacao->cor() }}">{{ $solicitacao->status }}</span></td>
                <td>
                    @if($solicitacao->user)
                        {{ $solicitacao->user->name }}<br>
                        <small class="text-muted">{{ $solicitacao->latestStatus()->created_at->format('d/m/Y H:i') }}</small>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-nowrap">
                    @include('aprovacoes._botoes')
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-4">Nenhuma solicitação encontrada.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $solicitacoes->links() }}
