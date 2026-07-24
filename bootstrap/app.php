<?php

use App\Http\Middleware\CheckPatrocinador;
use App\Http\Middleware\ControllerTokenAuth;
use App\Http\Middleware\LogoutUnauthorized;
use App\Http\Middleware\SecureHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecureHeadersMiddleware::class);
        $middleware->alias([
            'patrocinador' => CheckPatrocinador::class,
            'logout.unauthorized' => LogoutUnauthorized::class,
            'controller.token' => ControllerTokenAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
