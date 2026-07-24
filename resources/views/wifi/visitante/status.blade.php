@extends('laravel-usp-theme::master')

@section('title', 'Status da Solicitação - Wi-Fi FFLCH')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card mt-5">
            <div class="card-body py-5 text-center">
                @php
                    $badgeClass = match($wifiRequest->status) {
                        \App\Enums\WifiRequestStatus::APPROVED => 'success',
                        \App\Enums\WifiRequestStatus::REJECTED => 'danger',
                        \App\Enums\WifiRequestStatus::EXPIRED => 'secondary',
                        default => 'warning',
                    };
                    $icon = match($wifiRequest->status) {
                        \App\Enums\WifiRequestStatus::APPROVED => 'fa-wifi',
                        \App\Enums\WifiRequestStatus::REJECTED => 'fa-times-circle',
                        \App\Enums\WifiRequestStatus::EXPIRED => 'fa-clock',
                        default => 'fa-hourglass-half',
                    };
                @endphp

                <div class="bg-{{ $badgeClass }} text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 avatar-lg">
                    <i class="fas {{ $icon }} fa-2x"></i>
                </div>

                <h3 class="font-weight-bold">Status: {{ $wifiRequest->status->label() }}</h3>

                <div class="bg-light rounded p-3 d-inline-block my-4">
                    <small class="text-uppercase text-muted font-weight-bold d-block mb-1">Protocolo de Acompanhamento</small>
                    <strong class="text-primary protocol-id">{{ $wifiRequest->id }}</strong>
                </div>

                @if($wifiRequest->visitor)
                    <div class="bg-light rounded p-3 d-inline-block mb-4">
                        <small class="text-uppercase text-muted font-weight-bold d-block mb-1">Dispositivo (MAC)</small>
                        <strong class="text-primary">{{ $wifiRequest->visitor->client_mac }}</strong>
                    </div>
                @endif

                @if($wifiRequest->status === \App\Enums\WifiRequestStatus::PENDING)
                    <p class="text-muted mb-1">
                        Aguarde a aprovação de um funcionário ou docente da FFLCH.
                    </p>
                    <p class="text-muted small">
                        <i class="fas fa-sync-alt"></i> Atualize esta página em alguns minutos para verificar o status.
                    </p>
                @elseif($wifiRequest->status === \App\Enums\WifiRequestStatus::APPROVED)
                    <p class="text-success mb-1">
                        <i class="fas fa-check-circle"></i> Seu acesso foi aprovado!
                    </p>
                    @if($wifiRequest->expires_at)
                        <p class="text-muted small">
                            <i class="fas fa-clock"></i> Válido até <strong>{{ $wifiRequest->expires_at->format('d/m/Y H:i') }}</strong>.
                        </p>
                    @endif
                    <p class="text-muted small">
                        Conecte-se à rede Wi-Fi visitante para começar a navegar.
                    </p>
                @elseif($wifiRequest->status === \App\Enums\WifiRequestStatus::REJECTED)
                    <p class="text-danger mb-1">
                        <i class="fas fa-times-circle"></i> Sua solicitação foi rejeitada.
                    </p>
                    <p class="text-muted small">
                        Para mais informações, entre em contato com a administração.
                    </p>
                @elseif($wifiRequest->status === \App\Enums\WifiRequestStatus::EXPIRED)
                    <p class="text-secondary mb-1">
                        <i class="fas fa-clock"></i> Seu acesso expirou.
                    </p>
                    <p class="text-muted small">
                        Solicite um novo acesso preenchendo o formulário novamente.
                    </p>
                @endif

                <div class="mt-4">
                    @if($wifiRequest->status === \App\Enums\WifiRequestStatus::PENDING)
                        <a href="{{ route('wifi.visitante.status', ['id' => $wifiRequest->id]) }}" class="btn btn-outline-primary">
                            <i class="fas fa-sync-alt mr-1"></i> ATUALIZAR
                        </a>
                    @endif
                    <a href="{{ route('wifi.visitante.create', ['client_mac' => $wifiRequest->visitor->client_mac ?? '']) }}" class="btn btn-link">
                        <i class="fas fa-arrow-left"></i> VOLTAR AO INÍCIO
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
