<?php

return [

    'ssid' => env('WIFI_SSID', 'fflch'),

    'nbi_ip' => env('WIFI_NBI_IP', '10.10.10.10'),

    // Redes (CIDR) de onde /solicitar pode ser acessado. Ex.: "10.10.0.0/16,143.107.79.0/24"
    // Vazio = qualquer origem.
    // Proxies reversos confiáveis (ex.: Traefik). O IP real do cliente vem do X-Forwarded-For.
    // "*" = confiar em quem estiver na frente (só se o app não for acessível direto, sem o proxy).
    // Ou redes CIDR separadas por vírgula. Vazio = não confiar em proxy.
    'proxies_confiaveis' => env('TRUSTED_PROXIES') == '*'
        ? '*'
        : array_values(array_filter(array_map('trim', explode(',', env('TRUSTED_PROXIES', ''))))),

    'redes_permitidas' => array_values(array_filter(array_map('trim', explode(',', env('WIFI_REDES_PERMITIDAS', ''))))),

];
