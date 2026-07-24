<?php

use App\Http\Controllers\PatrocinadorWifiController;
use App\Http\Controllers\VisitanteWifiController;
use App\Http\Controllers\WifiStatusController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index']);


Route::prefix('solicitar')->middleware('logout.unauthorized')->name('wifi.visitante.')->group(function () {
    Route::get('/', [VisitanteWifiController::class, 'create'])->name('create');
    Route::post('/', [VisitanteWifiController::class, 'store'])->name('store')->middleware('throttle:solicitacao-wifi');
    Route::get('/sucesso/{id}', function (string $id) {
        return view('wifi.visitante.sucesso', compact('id'));
    })->name('sucesso');
    Route::get('/status/{id}', [VisitanteWifiController::class, 'status'])->name('status');
});

Route::middleware(['auth', 'patrocinador'])->name('wifi.patrocinador.')->group(function () {
    Route::get('/dashboard', [PatrocinadorWifiController::class, 'dashboard'])->name('dashboard');

    Route::prefix('aprovacoes')->group(function () {
        Route::get('/', [PatrocinadorWifiController::class, 'index'])->name('index');
        Route::get('/finalizadas', [PatrocinadorWifiController::class, 'finalizadas'])->name('finalizadas');
        Route::post('/{wifiRequest}/aprovar', [PatrocinadorWifiController::class, 'aprovar'])->name('aprovar');
        Route::patch('/{wifiRequest}/rejeitar', [PatrocinadorWifiController::class, 'rejeitar'])->name('rejeitar');
    });
});

Route::prefix('api/wifi')->middleware('controller.token')->group(function () {
    Route::get('/mac/{mac}', [WifiStatusController::class, 'status']);
    Route::get('/aprovados', [WifiStatusController::class, 'aprovados']);
    Route::get('/fila', [WifiStatusController::class, 'fila']);
});
