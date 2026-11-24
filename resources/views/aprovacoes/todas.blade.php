@extends('laravel-usp-theme::master')

@section('title', 'Todas as Solicitações')

@section('content')
<h3>Todas as Solicitações</h3>
<p class="text-muted">Solicitações de todos os visitantes, com quem aprovou ou rejeitou. Visível só para administradores.</p>

@include('aprovacoes._busca', ['placeholder' => 'Visitante, e-mail, CPF, MAC ou quem decidiu'])
@include('aprovacoes._tabela')
@endsection
