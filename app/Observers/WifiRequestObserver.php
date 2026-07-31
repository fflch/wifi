<?php declare(strict_types=1);

namespace App\Observers;

use App\Enums\WifiRequestStatus;
use App\Models\AuditLog;
use App\Models\WifiRequest;

class WifiRequestObserver
{
    public function creating(WifiRequest $wifiRequest): void
    {
        $wifiRequest->status ??= WifiRequestStatus::PENDING;
    }

    public function updated(WifiRequest $wifiRequest): void
    {
        if ($wifiRequest->wasChanged('status')) {
            $oldStatus = $wifiRequest->getOriginal('status');
            AuditLog::create([
                'actor_codpes' => auth()->user()?->codpes,
                'action' => "wifi_request.{$wifiRequest->status->value}",
                'target_id' => $wifiRequest->id,
                'payload' => [
                    'old_status' => $oldStatus instanceof WifiRequestStatus ? $oldStatus->value : $oldStatus,
                    'new_status' => $wifiRequest->status->value,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
