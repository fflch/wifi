@extends('laravel-usp-theme::master')

@section('title', 'Acesso Liberado - Wi-Fi FFLCH')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card text-center mt-5">
            <div class="card-body py-5">
                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4 avatar-lg">
                    <i class="fas fa-wifi fa-2x"></i>
                </div>

                <h3 class="font-weight-bold text-success">Acesso Liberado!</h3>

                <p class="text-muted mb-3">
                    Seu dispositivo foi aprovado e já pode navegar na rede Wi-Fi de visitantes da FFLCH-USP.
                </p>

                <div class="bg-light rounded p-3 d-inline-block mb-3">
                    <small class="text-uppercase text-muted font-weight-bold d-block mb-1">Dispositivo (MAC)</small>
                    <strong class="text-primary">{{ $client_mac }}</strong>
                </div>

                @if($expires_at ?? null)
                    <p class="text-muted mb-4">
                        <i class="fas fa-clock text-primary"></i>
                        Acesso válido até <strong>{{ $expires_at->format('d/m/Y H:i') }}</strong>.
                    </p>
                @else
                    <p class="text-muted mb-4">
                        <i class="fas fa-clock text-primary"></i>
                        Acesso sem expiração definida.
                    </p>
                @endif

                <div>
                    <p class="text-muted small">
                        <i class="fas fa-shield-alt text-primary"></i>
                        Conecte-se à rede Wi-Fi visitante e abra o navegador para começar a navegar.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
