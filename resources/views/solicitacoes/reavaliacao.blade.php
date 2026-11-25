@extends('layouts.portal')

@section('title', 'Segunda Avaliação')

@section('content')
<div class="card">
    <div class="card-body">
        @if(! $valido)
            <h1>Link indisponível</h1>
            <p class="muted">Este link já foi usado ou a solicitação já teve uma nova decisão.</p>
            <a href="/solicitar/{{ $solicitacao->id }}" class="btn btn-outline">Ver situação da solicitação</a>
        @else
            <h1>Pedir segunda avaliação</h1>
            <p class="muted">
                Sua solicitação foi rejeitada por <strong>{{ $solicitacao->user?->name }}</strong>.
                @if($solicitacao->mensagem())
                    Motivo: <i>{{ $solicitacao->mensagem() }}</i>
                @endif
            </p>
            <p class="muted">Explique por que o acesso deve ser liberado. Quem rejeitou receberá seu pedido por e-mail e a solicitação volta para análise.</p>

            @if($errors->any())
                <div class="alert">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ request()->getRequestUri() }}">
                @csrf
                <div class="field">
                    <label for="justificativa">JUSTIFICATIVA <span class="req">*</span></label>
                    <textarea name="justificativa" id="justificativa" rows="4" required minlength="10" maxlength="1000"
                        placeholder="Ex.: estou participando do evento X, organizado pela profa. Y">{{ old('justificativa') }}</textarea>
                </div>
                <button type="submit" class="btn">Enviar pedido</button>
            </form>
        @endif
    </div>
</div>
@endsection
