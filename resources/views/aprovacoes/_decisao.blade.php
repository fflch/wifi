{{--
    Aprovar (mensagem opcional) ou rejeitar (motivo obrigatório).
    Aprovar: autorizador, quando ainda não está aprovada. Rejeitar: gate 'rejeitar'.
--}}
@php
    $podeAprovar = $solicitacao->status != 'aprovado' && Gate::allows('autorizador');
    $podeRejeitar = Gate::allows('rejeitar', $solicitacao);
    $doFormulario = old('solicitacao') == $solicitacao->id;
@endphp

@if($podeAprovar || $podeRejeitar)
    <form method="POST">
        @csrf
        @method('PATCH')
        <input type="hidden" name="solicitacao" value="{{ $solicitacao->id }}">

        <textarea name="mensagem" rows="2" class="form-control mb-2"
            placeholder="Mensagem ao visitante ({{ $podeAprovar ? 'opcional ao aprovar, ' : '' }}obrigatória ao rejeitar)">{{ $doFormulario ? old('mensagem') : '' }}</textarea>

        @if($podeRejeitar)
            <div class="form-check mb-2">
                <input type="checkbox" name="devolver" value="1" class="form-check-input" id="devolver-{{ $solicitacao->id }}">
                <label class="form-check-label small" for="devolver-{{ $solicitacao->id }}">
                    Ao rejeitar, devolver para outra pessoa avaliar
                </label>
            </div>
        @endif

        <div class="row">
            @if($podeAprovar)
                <div class="col">
                    <button type="submit" formaction="/aprovacoes/{{ $solicitacao->id }}/aprovar" class="btn btn-success btn-lg btn-block"
                        onclick="return confirm('Liberar o acesso de {{ $solicitacao->visitante->nome }}?');">
                        <i class="fas fa-check"></i> Aprovar
                    </button>
                </div>
            @endif
            @if($podeRejeitar)
                <div class="col">
                    <button type="submit" formaction="/aprovacoes/{{ $solicitacao->id }}/rejeitar" class="btn btn-outline-danger btn-lg btn-block"
                        onclick="if (!this.form.mensagem.value.trim()) { alert('Informe o motivo da rejeição.'); this.form.mensagem.focus(); return false; } return confirm('Rejeitar {{ $solicitacao->visitante->nome }}?');">
                        <i class="fas fa-times"></i> Rejeitar
                    </button>
                </div>
            @endif
        </div>
    </form>
@endif
