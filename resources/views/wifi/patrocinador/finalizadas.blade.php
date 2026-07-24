@extends('laravel-usp-theme::master')

@section('title', 'Histórico de Solicitações - Wi-Fi FFLCH')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
@endsection

@section('content')
<div class="pt-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="font-weight-bold mb-1">Histórico de Solicitações</h3>
            <p class="text-muted mb-0">Registro completo de todas as solicitações já processadas pelo sistema.</p>
        </div>
        <div>
            <form action="{{ route('wifi.patrocinador.finalizadas') }}" method="GET" class="form-inline">
                <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por nome, email, CPF ou MAC..." value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                        @if($search)
                            <a href="{{ route('wifi.patrocinador.finalizadas') }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
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
                    <div class="text-muted text-uppercase small font-weight-bold">TOTAL FINALIZADAS</div>
                    <div class="h3 font-weight-bold mb-0">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="card border-left-success">
                <div class="card-body py-3">
                    <div class="text-muted text-uppercase small font-weight-bold">APROVADOS</div>
                    <div class="h3 font-weight-bold mb-0 text-success">{{ $stats['aprovados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="card border-left-danger">
                <div class="card-body py-3">
                    <div class="text-muted text-uppercase small font-weight-bold">REJEITADOS</div>
                    <div class="h3 font-weight-bold mb-0 text-danger">{{ $stats['rejeitados'] }}</div>
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
                        <th>Motivo</th>
                        <th>Status</th>
                        <th>Processado por</th>
                        <th>Validade</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidosFinalizados as $pedido)
                        <tr>
                            <td>
                                <span class="font-weight-bold text-primary">{{ $pedido->created_at->format('d M, Y') }}</span>
                                <br><small class="text-muted">{{ $pedido->created_at->format('H:i') }}</small>
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
                            <td class="text-cell-limited-lg">
                                <span title="{{ $pedido->reason }}">{{ Str::limit($pedido->reason, 80) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-pill px-3 py-2 {{ $pedido->badge_class }}">
                                    {{ $pedido->status->label() }}
                                </span>
                            </td>
                            <td>
                                @if($pedido->approver)
                                    <strong>{{ $pedido->approver->name }}</strong>
                                    <br><small class="text-muted">Codpes: {{ $pedido->approver->codpes }}</small>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                            <td>
                                @if($pedido->status->value === 'approved' && $pedido->expires_at)
                                    <span class="text-success font-weight-bold">{{ $pedido->expires_at->format('d/m/Y') }}</span>
                                    <br><small class="text-muted">{{ $pedido->expires_at->format('H:i') }}</small>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-history fa-3x mb-3"></i>
                                    <p class="mb-0">Nenhuma solicitação finalizada encontrada.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pedidosFinalizados->total() > 0)
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Mostrando <strong>{{ $pedidosFinalizados->count() }}</strong> de <strong>{{ $pedidosFinalizados->total() }}</strong> solicitações
            </small>
            {{ $pedidosFinalizados->links() }}
        </div>
        @endif
    </div>

    <div class="mt-3">
        <a href="{{ route('wifi.patrocinador.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left mr-1"></i> VOLTAR AO DASHBOARD
        </a>
    </div>
</div>
@endsection
