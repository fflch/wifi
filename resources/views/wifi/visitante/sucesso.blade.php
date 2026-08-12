@extends('laravel-usp-theme::master')

@section('title', 'Solicitação Recebida - Wi-Fi FFLCH')

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card text-center mt-5">
            <div class="card-body py-5">
                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 avatar-lg">
                    <i class="fas fa-check fa-2x"></i>
                </div>

                <h3 class="font-weight-bold">Solicitação Recebida!</h3>

                <p class="text-muted mb-3">
                    Sua solicitação de acesso à rede Wi-Fi foi recebida com sucesso.
                </p>

                <p class="text-muted mb-4">
                    <strong>Qualquer professor ou funcionário da FFLCH</strong> pode aprovar seu acesso.
                    Procure a pessoa responsável pela sua visita:
                </p>

                <ul class="text-left text-muted mb-4">
                    <li><strong>Professor visitante:</strong> procure o professor da FFLCH com quem está trabalhando.</li>
                    <li><strong>Participante de evento:</strong> procure o organizador do evento.</li>
                    <li><strong>Funcionário terceirizado (limpeza/vigilância):</strong> procure o encarregado responsável.</li>
                </ul>

                <div class="bg-light rounded p-3 d-inline-block mb-4">
                    <small class="text-uppercase text-muted font-weight-bold d-block mb-1">Seu Protocolo de Acompanhamento</small>
                    <strong class="text-primary protocol-id">{{ $id }}</strong>
                </div>

                <div>
                    <a href="{{ route('wifi.visitante.status', ['id' => $id]) }}" class="btn btn-primary mr-2">
                        <i class="fas fa-search mr-1"></i> ACOMPANHAR STATUS
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
