<?php declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\WifiRequest;

class WifiRequestPolicy
{
    public function gerenciar(User $user, WifiRequest $wifiRequest): bool
    {
        if ($user->hasPermissionTo('Servidor')) {
            return true;
        }

        $admins = config('senhaunica.admins', []);
        return in_array((string) $user->codpes, $admins, strict: true);
    }
}
