<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\WifiRequestService;
use App\Support\MacAddress;
use Illuminate\Http\JsonResponse;

class WifiStatusController extends Controller
{
    public function __construct(
        private readonly WifiRequestService $wifiService
    ) {}

    public function status(string $mac): JsonResponse
    {
        if (! MacAddress::isValid($mac)) {
            return response()->json([
                'error' => 'invalid_mac',
                'message' => 'O endereço MAC informado é inválido.',
            ], 400);
        }

        $status = $this->wifiService->statusForMac($mac);

        if (! $status) {
            return response()->json([
                'status' => 'unknown',
                'message' => 'MAC não cadastrado no sistema.',
            ], 404);
        }

        return response()->json($status);
    }

    public function aprovados(): JsonResponse
    {
        return response()->json([
            'status' => 'approved',
            'count' => count($this->wifiService->getAprovadosAtivos()),
            'macs' => $this->wifiService->getAprovadosAtivos(),
        ]);
    }

    public function fila(): JsonResponse
    {
        return response()->json([
            'status' => 'pending',
            'count' => count($this->wifiService->getFilaEspera()),
            'macs' => $this->wifiService->getFilaEspera(),
        ]);
    }
}
