@extends('layouts.portal')

@section('title', 'Limite de Solicitações Excedido')

@section('content')
<div class="card center">
    <div class="card-body">
        <div class="icon bg-warning">!</div>
        <h1>Limite de Solicitações Excedido</h1>
        <p class="muted">{{ $message ?? 'Você excedeu o limite de solicitações. Tente novamente em uma hora.' }}</p>
    </div>
</div>
@endsection
