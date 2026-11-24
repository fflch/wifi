@extends('layouts.portal')

@section('title', 'Solicitar Acesso')

@section('content')
<h1>Acesso à Rede Wi-Fi Visitante</h1>
<p class="muted">Preencha os dados abaixo para solicitar acesso à rede sem fio para visitantes da FFLCH-USP.</p>

@if($errors->any())
    <div class="alert">
        <strong>Verifique os campos abaixo:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="/solicitar" id="form">
    @csrf
    <input type="hidden" name="mac" value="{{ old('mac', $mac) }}">

    <div class="card">
        <div class="card-body">
            <h2><span class="step">01</span>Dados pessoais</h2>

            <div class="field">
                <label for="nome">NOME COMPLETO <span class="req">*</span></label>
                <input type="text" name="nome" id="nome" value="{{ old('nome') }}" placeholder="Seu nome completo" required autofocus>
            </div>

            <div class="row">
                <div class="field">
                    <label for="email">E-MAIL <span class="req">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="seu@email.com" required>
                </div>
                <div class="field">
                    <label for="documento">CPF OU PASSAPORTE <span class="req">*</span></label>
                    <input type="text" name="documento" id="documento" value="{{ old('documento') }}" placeholder="123.456.789-00 ou AB1234" required>
                    <span class="hint">Formato brasileiro ou passaporte estrangeiro</span>
                </div>
            </div>

            <div class="field">
                <label for="telefone">TELEFONE <span class="muted">(opcional)</span></label>
                <input type="text" name="telefone" id="telefone" value="{{ old('telefone') }}" placeholder="(11) 99999-9999">
            </div>
        </div>

        <div class="card-body">
            <h2><span class="step">02</span>Dados da solicitação</h2>

            <div class="field">
                <label for="motivo">MOTIVO DA VISITA / ATIVIDADE <span class="req">*</span></label>
                <textarea name="motivo" id="motivo" rows="3" placeholder="Descreva o motivo da sua visita ou atividade que realizará na FFLCH..." required>{{ old('motivo') }}</textarea>
                <span class="hint">Mínimo 10 caracteres</span>
            </div>
        </div>

        <div class="card-footer footer-flex">
            <small class="muted">
                Seus dados são processados de acordo com a LGPD e utilizados apenas para fins de controle de acesso à rede.
            </small>
            <button type="submit" class="btn" id="enviar">Enviar Solicitação</button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // Máscara de CPF: só formata quando são exatamente 11 dígitos (passaporte fica livre)
    var documento = document.getElementById('documento');
    documento.addEventListener('input', function () {
        var digitos = documento.value.replace(/\D/g, '');
        if (/^[\d.\-]+$/.test(documento.value) && digitos.length == 11) {
            documento.value = digitos.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        }
    });

    // Máscara de telefone: (11) 99999-9999
    var telefone = document.getElementById('telefone');
    telefone.addEventListener('input', function () {
        var v = telefone.value.replace(/\D/g, '').substring(0, 11);
        if (v.length > 7) v = '(' + v.substring(0, 2) + ') ' + v.substring(2, 7) + '-' + v.substring(7);
        else if (v.length > 2) v = '(' + v.substring(0, 2) + ') ' + v.substring(2);
        else if (v.length > 0) v = '(' + v;
        telefone.value = v;
    });

    // Evita clique duplo no envio
    document.getElementById('form').addEventListener('submit', function () {
        var botao = document.getElementById('enviar');
        botao.disabled = true;
        botao.textContent = 'Processando...';
    });
</script>
@endsection
