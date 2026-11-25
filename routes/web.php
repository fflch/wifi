<?php

use App\Http\Controllers\AprovacaoController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\SolicitacaoController;
use App\Http\Middleware\SomenteRedeWifi;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index']);

// Visitante (captive portal, sem login). Só de dentro da rede Wi-Fi (WIFI_REDES_PERMITIDAS)
Route::get('/solicitar', [SolicitacaoController::class, 'create'])->middleware(SomenteRedeWifi::class);
Route::post('/solicitar', [SolicitacaoController::class, 'store'])->middleware(SomenteRedeWifi::class);
Route::get('/solicitar/{solicitacao}', [SolicitacaoController::class, 'show']);

// Segunda avaliação: só pelo link assinado do e-mail de rejeição.
// 'signed:relative' assina só caminho + parâmetros (funciona atrás de proxy https)
Route::get('/solicitar/{solicitacao}/reavaliacao', [SolicitacaoController::class, 'reavaliacao'])
    ->name('reavaliacao')->middleware('signed:relative');
Route::post('/solicitar/{solicitacao}/reavaliacao', [SolicitacaoController::class, 'pedirReavaliacao'])
    ->middleware(['signed:relative', 'throttle:6,1']);

// Autorizador (servidor ou docente na Senha Única)
Route::get('/aprovacoes', [AprovacaoController::class, 'index']);
Route::get('/aprovacoes/minhas', [AprovacaoController::class, 'minhas']);
Route::get('/aprovacoes/todas', [AprovacaoController::class, 'todas']); // admin
Route::get('/aprovacoes/{solicitacao}', [AprovacaoController::class, 'show']);
Route::patch('/aprovacoes/{solicitacao}/aprovar', [AprovacaoController::class, 'aprovar']);
Route::patch('/aprovacoes/{solicitacao}/rejeitar', [AprovacaoController::class, 'rejeitar']);
Route::delete('/aprovacoes/{solicitacao}', [AprovacaoController::class, 'destroy']); // admin
