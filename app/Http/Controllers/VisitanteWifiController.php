<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\WifiRequestStatus;
use App\Http\Requests\SolicitarWifiRequest;
use App\Models\WifiRequest;
use App\Services\WifiRequestService;
use App\Support\MacAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
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
        ]);

        if ($validator->fails()) {
            request()->session()->flash('alert-info','Problema em coletar informações do dispositivo, por favor, desconecte da rede wifi e conecte-se novamente.');
        } else {
           $clientMac = MacAddress::normalize($request->input('client_mac'));
           $latest = $this->wifiService->latestStatusForMac($clientMac);

            if ($latest) {
                if (
                    $latest->status === WifiRequestStatus::APPROVED
                    && ($latest->expires_at === null || $latest->expires_at->gt(Carbon::now()))
                ) {
                    return view('wifi.visitante.liberado', [
                        'client_mac' => $clientMac,
                        'wifiRequest' => $latest,
                        'expires_at' => $latest->expires_at,
                    ]);
                }

                if (
                    $latest->status === WifiRequestStatus::PENDING
                    || ($latest->status === WifiRequestStatus::APPROVED && $latest->expires_at?->lte(Carbon::now()))
                ) {
                    return view('wifi.visitante.aguarde', [
                        'client_mac' => $clientMac,
                        'wifiRequest' => $latest,
                    ]);
                }
            }
            return view('wifi.visitante.solicitar', ['client_mac' => $clientMac]);
        }
        return redirect('/');
        
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
        $wifiRequest = WifiRequest::with('visitor')->findOrFail($id);

        return view('wifi.visitante.status', compact('wifiRequest'));
    }
}
