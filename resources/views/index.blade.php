@extends('laravel-usp-theme::master')

@section('title', 'Wi-Fi FFLCH')

@section('styles')
@parent
<style>
.feature-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    border-radius: 12px;
    overflow: hidden;
}
.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(39,62,116,0.15);
}
.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.avatar-circle-sm {
    width: 48px;
    height: 48px;
}
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="text-center py-4">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 avatar-circle" style="background-color: #273e74 !important;">
                <i class="fas fa-wifi fa-lg"></i>
            </div>
            <h2 class="font-weight-bold">Wi-Fi FFLCH</h2>
            <p class="text-muted">Sistema de solicitação e aprovação de acesso à rede sem fio para visitantes da Faculdade de Filosofia, Letras e Ciências Humanas da USP.</p>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card feature-card h-100 text-center">
                    <div class="card-body d-flex flex-column align-items-center py-5">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 avatar-circle" style="background-color: #273e74 !important;">
                            <i class="fas fa-user-plus fa-lg"></i>
                        </div>
                        <h5 class="font-weight-bold">Solicitar Acesso</h5>
                        <p class="text-muted mb-4">Solicite acesso à rede Wi-Fi de visitantes informando seus dados e o dispositivo que será utilizado.</p>
                        <a href="{{ route('wifi.visitante.create') }}" class="btn btn-primary mt-auto px-4 font-weight-bold">SOLICITAR</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card feature-card h-100 text-center">
                    <div class="card-body d-flex flex-column align-items-center py-5">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 avatar-circle" style="background-color: #273e74 !important;">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                        <h5 class="font-weight-bold">Aprovações</h5>
                        <p class="text-muted mb-4">Funcionários e docentes: gerencie as solicitações de acesso pendentes.</p>
                        @auth
                            <a href="{{ route('wifi.patrocinador.index') }}" class="btn btn-primary mt-auto px-4 font-weight-bold">APROVAÇÕES</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary mt-auto px-4 font-weight-bold">ENTRAR</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
