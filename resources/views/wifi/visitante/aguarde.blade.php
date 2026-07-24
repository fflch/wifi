@extends('laravel-usp-theme::master')

@section('title', 'Aguardando Aprovação - Wi-Fi FFLCH')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card text-center mt-5">
            <div class="card-body py-5">
                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 avatar-lg">
                    <i class="fas fa-hourglass-half fa-2x"></i>
                </div>

                <h3 class="font-weight-bold">Aguardando Aprovação</h3>

                <p class="text-muted mb-3">
                    Recebemos sua solicitação de acesso à rede Wi-Fi de visitantes da FFLCH-USP.
                </p>

                <p class="text-muted mb-4">
                    Aguarde a aprovação de um funcionário ou docente para que seu dispositivo seja liberado na rede.
                </p>

                <div class="bg-light rounded p-3 d-inline-block mb-3">
                    <small class="text-uppercase text-muted font-weight-bold d-block mb-1">Dispositivo (MAC)</small>
                    <strong class="text-primary">{{ $client_mac }}</strong>
                </div>

                @if(isset($wifiRequest) && $wifiRequest->id)
                    <div class="bg-light rounded p-3 d-inline-block mb-3">
                        <small class="text-uppercase text-muted font-weight-bold d-block mb-1">Protocolo de Acompanhamento</small>
                        <strong class="text-primary protocol-id">{{ $wifiRequest->id }}</strong>
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('wifi.visitante.status', ['id' => $wifiRequest->id ?? '']) }}" class="btn btn-outline-primary">
                        <i class="fas fa-sync-alt mr-1"></i> VERIFICAR STATUS
                    </a>
                    <a href="{{ route('wifi.visitante.create', ['client_mac' => $client_mac]) }}" class="btn btn-link ml-2">
                        <i class="fas fa-arrow-left"></i> VOLTAR AO INÍCIO
                    </a>
                </div>

                <p class="text-muted small mt-4">
                    <i class="fas fa-info-circle"></i>
                    Você já preencheu o formulário. Este passo não precisa ser repetido.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
