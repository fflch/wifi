<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\WifiRequestStatus;
use App\Http\Requests\SolicitarWifiRequest;
use App\Models\WifiRequest;
use App\Services\WifiRequestService;
use App\Support\MacAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitanteWifiController extends Controller
{
    public function __construct(
        private readonly WifiRequestService $wifiService
    ) {}

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_mac' => ['required', 'mac_address'],
            'NbiIP' => ['required', 'ip'],
            'ssid' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            abort(403, 'Acesso negado. Parâmetros obrigatórios ausentes ou inválidos.');
        }

        $allowedSsid = config('wifi.ssid');
        $allowedNbiIp = config('wifi.nbi_ip');

        if ($request->input('ssid') !== $allowedSsid) {
            abort(403, 'SSID inválido.');
        }

        if ($request->input('NbiIP') !== $allowedNbiIp) {
            abort(403, 'IP da controladora inválido.');
        }

        $clientMac = MacAddress::normalize($request->input('client_mac'));
        $latest = $this->wifiService->latestStatusForMac($clientMac);

        if ($latest) {
            if ($latest->status === WifiRequestStatus::APPROVED) {
                return view('wifi.visitante.liberado', [
                    'client_mac' => $clientMac,
                    'wifiRequest' => $latest,
                ]);
            }

            if ($latest->status === WifiRequestStatus::PENDING) {
                return view('wifi.visitante.aguarde', [
                    'client_mac' => $clientMac,
                    'wifiRequest' => $latest,
                ]);
            }

            if ($latest->status === WifiRequestStatus::REJECTED) {
                $rejecterName = $latest->rejecter?->name;

                $latest->update([
                    'status' => WifiRequestStatus::PENDING,
                    'approved_by' => null,
                    'rejected_by' => null,
                    'expires_at' => null,
                ]);

                $latest->load('rejecter', 'visitor');

                return view('wifi.visitante.aguarde', [
                    'client_mac' => $clientMac,
                    'wifiRequest' => $latest,
                    'reaberto' => true,
                    'rejeitado_por' => $rejecterName,
                ]);
            }
        }

        return view('wifi.visitante.solicitar', [
            'client_mac' => $clientMac,
        ]);
    }

    public function store(SolicitarWifiRequest $request): RedirectResponse
    {
        $dados = $request->validated();

        $wifiRequest = $this->wifiService->solicitarAcesso(
            dadosVisitante: $request->only(['name', 'email', 'document', 'phone', 'client_mac']),
            motivo: $dados['reason']
        );

        return redirect()->route('wifi.visitante.sucesso', ['id' => $wifiRequest->id]);
    }

    public function status(string $id): View
    {
        $wifiRequest = WifiRequest::with('visitor', 'rejecter')->findOrFail($id);

        return view('wifi.visitante.status', compact('wifiRequest'));
    }
}
