<?php

use App\Http\Controllers\AprovacaoController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\SolicitacaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index']);

// Visitante (captive portal, sem login)
Route::get('/solicitar', [SolicitacaoController::class, 'create']);
Route::post('/solicitar', [SolicitacaoController::class, 'store'])->middleware('throttle:solicitacao-wifi');
Route::get('/solicitar/{solicitacao}', [SolicitacaoController::class, 'show']);

// Autorizador (servidor ou docente na Senha Única)
Route::get('/aprovacoes', [AprovacaoController::class, 'index']);
Route::get('/aprovacoes/minhas', [AprovacaoController::class, 'minhas']);
Route::get('/aprovacoes/todas', [AprovacaoController::class, 'todas']); // admin
Route::get('/aprovacoes/{solicitacao}', [AprovacaoController::class, 'show']);
Route::patch('/aprovacoes/{solicitacao}/aprovar', [AprovacaoController::class, 'aprovar']);
Route::patch('/aprovacoes/{solicitacao}/rejeitar', [AprovacaoController::class, 'rejeitar']);
