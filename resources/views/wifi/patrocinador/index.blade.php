@extends('laravel-usp-theme::master')

@section('title', 'Aprovações de Wi-Fi Pendentes')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
@endsection

@section('content')
<div class="pt-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="font-weight-bold mb-1">Aprovações Pendentes</h3>
            <p class="text-muted mb-0">Os visitantes abaixo solicitaram acesso à rede Wi-Fi e aguardam autorização.</p>
        </div>
        <div>
            <form action="{{ route('wifi.patrocinador.index') }}" method="GET" class="form-inline">
                <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por nome, email, CPF ou MAC..." value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                        @if($search)
                            <a href="{{ route('wifi.patrocinador.index') }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-2">
            <div class="card">
                <div class="card-body py-3">
                    <div class="text-muted text-uppercase small font-weight-bold">TOTAL PENDENTE</div>
                    <div class="h3 font-weight-bold mb-0">{{ $totalPendente }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="card">
                <div class="card-body py-3">
                    <div class="text-muted text-uppercase small font-weight-bold">HOJE</div>
                    <div class="h3 font-weight-bold mb-0">{{ $hojePendente }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="card border-left-primary">
                <div class="card-body py-3">
                    <div class="text-muted text-uppercase small font-weight-bold">DISPOSITIVOS LIBERADOS</div>
                    <div class="h3 font-weight-bold mb-0 text-primary">{{ $aprovadosHoje }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Data</th>
                        <th>Visitante</th>
                        <th>MAC</th>
                        <th>Documento</th>
                        <th>Motivo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidosPendentes as $pedido)
                        <tr>
                            <td>
                                <span class="font-weight-bold text-primary">{{ $pedido->created_at->format('d M, Y') }}</span>
                                <br><small class="text-muted">{{ $pedido->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-2 avatar-circle">
                                        {{ $pedido->visitor->initials }}
                                    </div>
                                    <div>
                                        <strong>{{ $pedido->visitor->name }}</strong>
                                        <br><small class="text-muted">{{ $pedido->visitor->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($pedido->visitor->client_mac)
                                    <code class="text-monospace font-weight-bold text-dark">{{ $pedido->visitor->client_mac }}</code>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                            <td>{{ $pedido->visitor->formatted_document }}</td>
                            <td class="text-cell-limited">{{ $pedido->reason }}</td>
                            <td>
                                <div class="d-flex align-items-center action-group">
                                    <form action="{{ route('wifi.patrocinador.aprovar', $pedido->id) }}" method="POST" class="d-flex align-items-center">
                                        @csrf
                                        <select name="horas" class="form-control form-control-sm select-auto">
                                            <option value="4">4h</option>
                                            <option value="8">8h</option>
                                            <option value="12" selected>12h</option>
                                            <option value="24">24h</option>
                                        </select>
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Confirmar liberação de acesso para {{ $pedido->visitor->name }}?')">APROVAR</button>
                                    </form>
                                    <form action="{{ route('wifi.patrocinador.rejeitar', $pedido->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="_method" value="PATCH">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Tem certeza que deseja REJEITAR esta solicitação?')">REJEITAR</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                        <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">Nenhum pedido pendente no momento.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pedidosPendentes->total() > 0)
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Mostrando <strong>{{ $pedidosPendentes->count() }}</strong> de <strong>{{ $pedidosPendentes->total() }}</strong> solicitações
            </small>
            {{ $pedidosPendentes->links() }}
        </div>
        @endif
    </div>

    <div class="text-right mt-3">
        <a href="{{ route('wifi.patrocinador.index') }}" class="btn btn-primary">
            <i class="fas fa-sync-alt mr-1"></i> ATUALIZAR LISTA
        </a>
    </div>
</div>
@endsection
