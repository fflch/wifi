<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\WifiRequestStatus;
use App\Models\WifiRequest;
use App\Services\WifiRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatrocinadorWifiController extends Controller
{
    public function __construct(
        private readonly WifiRequestService $wifiService
    ) {}

    private function padStats(array $stats): array
    {
        return array_map(fn ($value) => str_pad((string) $value, 2, '0', STR_PAD_LEFT), $stats);
    }

    public function dashboard(): View
    {
        $stats = $this->padStats($this->wifiService->getFullStats());

        return view('wifi.patrocinador.dashboard', compact('stats'));
    }

    public function index(Request $request): View
    {
        $search = $request->query('search');
        $query = WifiRequest::where('status', WifiRequestStatus::PENDING);

        if ($search) {
            $query->whereHas('visitor', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%")
                    ->orWhere('client_mac', 'like', "%{$search}%");
            });
        }

        $stats = $this->padStats($this->wifiService->getIndexStats());

        $pedidosPendentes = $query->with('visitor')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('wifi.patrocinador.index', array_merge([
            'pedidosPendentes' => $pedidosPendentes,
            'search' => $search,
        ], $stats));
    }

    public function minhasAprovacoes(Request $request): View
    {
        $search = $request->query('search');
        $user = auth()->user();

        $query = WifiRequest::where(function ($q) use ($user) {
                $q->where('approved_by', $user->id)
                  ->orWhere('rejected_by', $user->id);
            })
            ->with(['visitor', 'approver', 'rejecter'])
            ->latest();

        if ($search) {
            $query->whereHas('visitor', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('document', 'like', "%{$search}%")
                    ->orWhere('client_mac', 'like', "%{$search}%");
            });
        }

        $minhasAprovacoes = $query->paginate(15)->withQueryString();

        return view('wifi.patrocinador.minhas-aprovacoes', compact('minhasAprovacoes', 'search'));
    }

    public function aprovar(Request $request, WifiRequest $wifiRequest): RedirectResponse
    {
        $this->authorize('gerenciar', $wifiRequest);

        $this->wifiService->aprovarAcesso(
            requestId: $wifiRequest->id,
            patrocinador: auth()->user(),
        );

        return back()->with('alert-success', 'Acesso liberado e enviado para a controladora Wi-Fi.');
    }

    public function rejeitar(WifiRequest $wifiRequest): RedirectResponse
    {
        $this->authorize('gerenciar', $wifiRequest);

        $this->wifiService->rejeitarAcesso($wifiRequest->id, auth()->user());

        return back()->with('alert-info', 'Solicitação de acesso rejeitada.');
    }

    public function rejeitarAprovado(WifiRequest $wifiRequest): RedirectResponse
    {
        $this->authorize('gerenciar', $wifiRequest);

        $this->wifiService->rejeitarAprovado($wifiRequest->id, auth()->user());

        return back()->with('alert-info', 'Aprovação revogada. A solicitação foi rejeitada.');
    }
}
