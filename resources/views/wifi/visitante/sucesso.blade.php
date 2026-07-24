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

                <p class="text-muted mb-4">
                    Aguarde a aprovação de um funcionário ou docente da FFLCH, para que seu dispositivo seja liberado na rede.
                </p>

                <div class="bg-light rounded p-3 d-inline-block mb-4">
                    <small class="text-uppercase text-muted font-weight-bold d-block mb-1">Seu Protocolo de Acompanhamento</small>
                    <strong class="text-primary protocol-id">{{ $id }}</strong>
                </div>

                <div>
                    <a href="{{ route('wifi.visitante.status', ['id' => $id]) }}" class="btn btn-primary mr-2">
                        <i class="fas fa-search mr-1"></i> ACOMPANHAR STATUS
                    </a>
                    <a href="{{ route('wifi.visitante.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left mr-1"></i> VOLTAR AO INÍCIO
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
