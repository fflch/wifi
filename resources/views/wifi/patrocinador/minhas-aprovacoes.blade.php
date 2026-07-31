@extends('laravel-usp-theme::master')

@section('title', 'Minhas Aprovações - Wi-Fi FFLCH')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
@endsection

@section('content')
<div class="pt-4">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="font-weight-bold mb-1">Minhas Aprovações</h3>
            <p class="text-muted mb-0">Solicitações que você aprovou ou rejeitou.</p>
        </div>
        <div>
            <form action="{{ route('wifi.patrocinador.minhas-aprovacoes') }}" method="GET" class="form-inline">
                <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por nome, email, CPF ou MAC..." value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                        @if($search)
                            <a href="{{ route('wifi.patrocinador.minhas-aprovacoes') }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </div>
            </form>
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
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($minhasAprovacoes as $pedido)
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
                                @if($pedido->status === \App\Enums\WifiRequestStatus::APPROVED && $pedido->approved_by === auth()->id())
                                    <form action="{{ route('wifi.patrocinador.rejeitar-aprovado', $pedido->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="_method" value="PATCH">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Tem certeza que deseja REJEITAR esta solicitação já aprovada?')">REJEITAR</button>
                                    </form>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-history fa-3x mb-3"></i>
                                    <p class="mb-0">Nenhuma solicitação encontrada.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($minhasAprovacoes->total() > 0)
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Mostrando <strong>{{ $minhasAprovacoes->count() }}</strong> de <strong>{{ $minhasAprovacoes->total() }}</strong> solicitações
            </small>
            {{ $minhasAprovacoes->links() }}
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
