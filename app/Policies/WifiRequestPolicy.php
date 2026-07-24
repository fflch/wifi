<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\WifiRequest;

class WifiRequestPolicy
{
    /**
     * Apenas usuarios da lista de admins (SENHAUNICA_ADMINS) podem gerenciar solicitacoes.
     */
    public function gerenciar(User $user, WifiRequest $wifiRequest): bool
    {
        $admins = config("senhaunica.admins", []);

        return in_array((string) $user->codpes, $admins, strict: true);
    }
}
