@extends('laravel-usp-theme::master')

@section('title', 'Minhas Aprovações')

@section('content')
<h3>Minhas Aprovações</h3>
<p class="text-muted">Solicitações que você aprovou ou rejeitou.</p>

@include('aprovacoes._busca')
@include('aprovacoes._tabela')
@endsection
