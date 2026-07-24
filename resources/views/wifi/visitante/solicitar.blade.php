@extends('laravel-usp-theme::master')

@section('title', 'Solicitar Acesso Wi-Fi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="mt-4 mb-4">
            <h3 class="font-weight-bold">Acesso à Rede Wi-Fi Visitante</h3>
            <p class="text-muted">Preencha os dados abaixo para solicitar acesso à rede sem fio para visitantes da FFLCH-USP.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Verifique os campos abaixo:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('wifi.visitante.store') }}" method="POST" id="wifiForm">
            @csrf
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase text-primary font-weight-bold">
                        <span class="badge badge-primary mr-2">01</span>DADOS PESSOAIS
                    </h5>

                    <div class="form-group">
                        <label for="name">NOME COMPLETO <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Seu nome completo" required autofocus>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">E-MAIL <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="seu@email.com" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="document">CPF OU PASSAPORTE <span class="text-danger">*</span></label>
                            <input type="text" name="document" id="document" class="form-control" value="{{ old('document') }}" placeholder="123.456.789-00 ou AB1234" required>
                            <small class="form-text text-muted">Formato brasileiro ou passaporte estrangeiro</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">TELEFONE <span class="text-muted font-weight-normal">(opcional)</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="(11) 99999-9999">
                    </div>
                </div>

                <div class="card-body border-top">
                    <h5 class="card-title text-uppercase text-primary font-weight-bold">
                        <span class="badge badge-primary mr-2">02</span>DADOS DA SOLICITAÇÃO
                    </h5>

                    <div class="form-group">
                        <label for="reason">MOTIVO DA VISITA / ATIVIDADE <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Descreva o motivo da sua visita ou atividade que realizará na FFLCH..." required>{{ old('reason') }}</textarea>
                        <small class="form-text text-muted">Mínimo 10 caracteres</small>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt text-primary"></i>
                        Seus dados são processados de acordo com a LGPD e utilizados apenas para fins de controle de acesso à rede.
                    </small>
                    <input type="hidden" name="client_mac" value="{{ $client_mac }}">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="btnText">Enviar Solicitação &rarr;</span>
                        <span id="btnLoading" style="display: none;">
                            <i class="fas fa-circle-notch fa-spin mr-1"></i>Processando...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('javascripts_bottom')
@parent
<script>
$(document).ready(function() {
    $('#document').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        }
        $(this).val(value);
    });

    $('#phone').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length > 0) {
            if (value.length <= 2) {
                value = '(' + value;
            } else if (value.length <= 7) {
                value = '(' + value.substring(0, 2) + ') ' + value.substring(2);
            } else {
                value = '(' + value.substring(0, 2) + ') ' + value.substring(2, 7) + '-' + value.substring(7, 11);
            }
        }
        $(this).val(value);
    });

    $('#wifiForm').on('submit', function() {
        $('#submitBtn').prop('disabled', true);
        $('#btnText').hide();
        $('#btnLoading').show();
    });
});
</script>
@endsection
