<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\WifiRequestService;
use Illuminate\Console\Command;

class ExpirarAcessosWifi extends Command
{
    protected $signature = 'wifi:expirar';

    protected $description = 'Transita as solicitações Wi-Fi APPROVED cujo expires_at já passou para EXPIRED.';

    public function handle(WifiRequestService $wifiService): int
    {
        $total = $wifiService->expirarAprovados();

        $this->info("Acessos expirados: {$total}");

        return self::SUCCESS;
    }
}
