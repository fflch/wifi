<?php

$menu = [
    [
        'text' => '<i class="fas fa-chart-pie"></i> Dashboard',
        'url' => '/dashboard',
        'can' => 'patrocinador',
    ],
    [
        'text' => '<i class="fas fa-clock"></i> Aprovações Pendentes',
        'url' => '/aprovacoes',
        'can' => 'patrocinador',
    ],
    [
        'text' => '<i class="fas fa-history"></i> Histórico',
        'url' => '/aprovacoes/finalizadas',
        'can' => 'patrocinador',
    ],
];

$right_menu = [
    [
        'key' => 'senhaunica-socialite',
    ],
];

return [
    'title' => config('app.name'),
    'skin' => env('USP_THEME_SKIN', 'uspdev'),
    'session_key' => 'laravel-usp-theme',
    'app_url' => config('app.url'),
    'logout_method' => 'POST',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'menu' => $menu,
    'right_menu' => $right_menu,
    'mensagensFlash' => true,
    'container' => 'container-fluid',
];
