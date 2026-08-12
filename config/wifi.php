<?php

return [

    'controller_token' => env('WIFI_CONTROLLER_TOKEN', ''),

    'access_validity_hours' => (int) env('WIFI_ACCESS_VALIDITY_HOURS', 12),

    'ssid' => env('WIFI_SSID', 'fflch'),

    'nbi_ip' => env('WIFI_NBI_IP', '143.107.79.85'),

];
