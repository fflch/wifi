<?php

namespace App\Console\Commands;

use App\Models\RadCheck;
use App\Models\Visitante;
use Illuminate\Console\Command;

class SincronizaRadius extends Command
{
    protected $signature = 'app:sincroniza-radius';

    protected $description = 'Refaz a tabela radcheck do FreeRADIUS a partir das solicitações aprovadas';

    public function handle()
    {
        RadCheck::truncate();

        foreach (Visitante::all() as $visitante) {
            $visitante->sincronizarRadius();
        }

        $this->info(RadCheck::count().' MAC(s) liberado(s) na radcheck.');
    }
}
