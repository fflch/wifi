<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\WifiRequestStatus;
use App\Models\User;
use App\Models\Visitor;
use App\Models\WifiRequest;
use App\Support\MacAddress;
use Illuminate\Support\Carbon;

class WifiRequestService
{
    public function solicitarAcesso(array $dadosVisitante, string $motivo): WifiRequest
    {
        $mac = MacAddress::normalize($dadosVisitante['client_mac'] ?? '');

        $visitor = Visitor::where('client_mac', $mac)->first();

        if ($visitor) {
            $visitor->update([
                'name' => $dadosVisitante['name'],
                'email' => $dadosVisitante['email'],
                'document' => $dadosVisitante['document'],
                'phone' => $dadosVisitante['phone'] ?? null,
            ]);
        } else {
            $visitor = Visitor::create([
                'name' => $dadosVisitante['name'],
                'email' => $dadosVisitante['email'],
                'document' => $dadosVisitante['document'],
                'phone' => $dadosVisitante['phone'] ?? null,
                'client_mac' => $mac,
            ]);
        }

        $wifiRequest = WifiRequest::create([
            'visitor_id' => $visitor->id,
            'reason' => $motivo,
            'status' => WifiRequestStatus::PENDING,
        ]);

        return $wifiRequest;
    }

    public function aprovarAcesso(string $requestId, User $patrocinador): WifiRequest
    {
        $wifiRequest = WifiRequest::findOrFail($requestId);

        $wifiRequest->update([
            'status' => WifiRequestStatus::APPROVED,
            'approved_by' => $patrocinador->id,
            'rejected_by' => null,
            'expires_at' => null,
        ]);

        return $wifiRequest;
    }

    public function rejeitarAcesso(string $requestId, User $patrocinador): WifiRequest
    {
        $wifiRequest = WifiRequest::findOrFail($requestId);

        $wifiRequest->update([
            'status' => WifiRequestStatus::REJECTED,
            'approved_by' => null,
            'rejected_by' => $patrocinador->id,
        ]);

        return $wifiRequest;
    }

    public function rejeitarAprovado(string $requestId, User $patrocinador): WifiRequest
    {
        $wifiRequest = WifiRequest::findOrFail($requestId);

        if ((int) $wifiRequest->approved_by !== (int) $patrocinador->id) {
            abort(403, 'Você só pode revogar aprovações feitas por você.');
        }

        $wifiRequest->update([
            'status' => WifiRequestStatus::REJECTED,
            'approved_by' => null,
            'rejected_by' => $patrocinador->id,
            'expires_at' => null,
        ]);

        return $wifiRequest;
    }

    public function getFullStats(): array
    {
        return [
            'total' => WifiRequest::where('status', '!=', WifiRequestStatus::PENDING)->count(),
            'pendentes' => WifiRequest::where('status', WifiRequestStatus::PENDING)->count(),
            'aprovados' => WifiRequest::where('status', WifiRequestStatus::APPROVED)->count(),
            'rejeitados' => WifiRequest::where('status', WifiRequestStatus::REJECTED)->count(),
            'hoje' => WifiRequest::whereDate('created_at', Carbon::today())->count(),
        ];
    }

    public function getIndexStats(): array
    {
        return [
            'totalPendente' => WifiRequest::where('status', WifiRequestStatus::PENDING)->count(),
            'hojePendente' => WifiRequest::where('status', WifiRequestStatus::PENDING)
                ->whereDate('created_at', Carbon::today())
                ->count(),
            'aprovadosHoje' => WifiRequest::where('status', WifiRequestStatus::APPROVED)
                ->whereDate('updated_at', Carbon::today())
                ->count(),
        ];
    }

    public function statusForMac(string $mac): ?array
    {
        $normalized = MacAddress::normalize($mac);

        if (! MacAddress::isValid($normalized)) {
            return null;
        }

        $visitor = Visitor::where('client_mac', $normalized)->first();

        if (! $visitor) {
            return null;
        }

        $latestRequest = WifiRequest::where('visitor_id', $visitor->id)
            ->latest()
            ->first();

        if (! $latestRequest) {
            return null;
        }

        return [
            'mac' => $normalized,
            'visitor_id' => $visitor->id,
            'request_id' => $latestRequest->id,
            'status' => $latestRequest->status->value,
            'expires_at' => $latestRequest->expires_at?->toIso8601String(),
        ];
    }

    public function getAprovadosAtivos(): array
    {
        $requests = WifiRequest::with('visitor')
            ->where('status', WifiRequestStatus::APPROVED)
            ->get();

        return $requests->map(fn (WifiRequest $r) => [
            'mac' => $r->visitor->client_mac,
            'visitor' => $r->visitor->name,
        ])->values()->toArray();
    }

    public function getFilaEspera(): array
    {
        $requests = WifiRequest::with('visitor')
            ->where('status', WifiRequestStatus::PENDING)
            ->latest()
            ->get();

        return $requests->map(fn (WifiRequest $r) => [
            'mac' => $r->visitor->client_mac,
            'visitor' => $r->visitor->name,
            'requested_at' => $r->created_at->toIso8601String(),
        ])->values()->toArray();
    }

    public function latestStatusForMac(string $mac): ?WifiRequest
    {
        $normalized = MacAddress::normalize($mac);

        if (! MacAddress::isValid($normalized)) {
            return null;
        }

        $visitor = Visitor::where('client_mac', $normalized)->first();

        if (! $visitor) {
            return null;
        }

        return WifiRequest::where('visitor_id', $visitor->id)
            ->latest()
            ->first();
    }
}
