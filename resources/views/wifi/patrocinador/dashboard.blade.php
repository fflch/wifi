@extends('laravel-usp-theme::master')

@section('title', 'Dashboard - Wi-Fi FFLCH')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
@endsection

@section('content')
<div class="pt-4">
    <div class="text-center mb-4">
        <h3 class="font-weight-bold">Painel de Controle</h3>
        <p class="text-muted">Visão geral das solicitações de acesso Wi-Fi FFLCH</p>
    </div>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase small font-weight-bold">Pendentes</div>
                    <div class="display-4 font-weight-bold">{{ $stats['pendentes'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase small font-weight-bold">Aprovados</div>
                    <div class="display-4 font-weight-bold">{{ $stats['aprovados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-danger h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase small font-weight-bold">Rejeitados</div>
                    <div class="display-4 font-weight-bold">{{ $stats['rejeitados'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase small font-weight-bold">Solicitações Hoje</div>
                    <div class="display-4 font-weight-bold">{{ $stats['hoje'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-6 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                    <h5 class="font-weight-bold">Aprovações Pendentes</h5>
                    <p class="text-muted">Gerencie as solicitações que aguardam uma decisão para liberação de acesso.</p>
                    <a href="{{ route('wifi.patrocinador.index') }}" class="btn btn-primary">Ir para Pendentes</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-history fa-3x text-secondary mb-3"></i>
                    <h5 class="font-weight-bold">Minhas Aprovações</h5>
                    <p class="text-muted">Consulte solicitações que você aprovou ou rejeitou.</p>
                    <a href="{{ route('wifi.patrocinador.minhas-aprovacoes') }}" class="btn btn-outline-secondary">Ver Minhas Aprovações</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
