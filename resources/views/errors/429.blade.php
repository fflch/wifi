@extends('laravel-usp-theme::master')

@section('title', 'Limite de Solicitações Excedido')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 text-center mt-5">
        <div class="mb-4">
            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 5rem;"></i>
        </div>
        <h3>Limite de Solicitações Excedido</h3>
        <p class="lead">{{ $message ?? 'Você excedeu o limite de solicitações. Tente novamente em uma hora.' }}</p>
        <a href="{{ route('wifi.visitante.create') }}" class="btn btn-primary mt-3">Voltar ao Início</a>
    </div>
</div>
@endsection
