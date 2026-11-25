<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Libera a rota só para IPs das redes em WIFI_REDES_PERMITIDAS (CIDR, separados por vírgula).
 * Variável vazia = sem restrição.
 */
class SomenteRedeWifi
{
    public function handle(Request $request, Closure $next): Response
    {
        $redes = config('wifi.redes_permitidas');

        if ($redes && ! IpUtils::checkIp($request->ip(), $redes)) {
            // O IP aparece na mensagem para ajudar a descobrir se chega o IP do NAT ou o público
            abort(403, 'Acesso permitido somente pela rede Wi-Fi de visitantes da FFLCH. Seu IP: '.$request->ip());
        }

        return $next($request);
    }
}
