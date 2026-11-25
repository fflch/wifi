<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pendentes há mais de 1 hora são rejeitadas automaticamente
Schedule::command('app:rejeita-pendentes')->everyMinute()->withoutOverlapping();
