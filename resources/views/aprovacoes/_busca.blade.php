<form class="form-inline mb-3">
    <input type="text" name="search" value="{{ request('search') }}" class="form-control mr-2" placeholder="{{ $placeholder ?? 'Nome, e-mail, CPF ou MAC' }}">
    <button type="submit" class="btn btn-outline-primary">Pesquisar</button>
    @if(request('search'))
        <a href="{{ url()->current() }}" class="btn btn-link">Limpar</a>
    @endif
</form>
