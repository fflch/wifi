<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ControllerTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = config('wifi.controller_token');

        if (empty($expectedToken)) {
            return response()->json([
                'error' => 'controller_token_not_configured',
                'message' => 'Token da controladora Wi-Fi não configurado no servidor.',
            ], 503);
        }

        $provided = $request->header('X-Controller-Token');

        if (! is_string($provided) || ! hash_equals($expectedToken, $provided)) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'Token da controladora inválido ou ausente.',
            ], 401);
        }

        return $next($request);
    }
}
