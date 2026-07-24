<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\WifiRequestStatus;
use App\Models\Visitor;
use App\Models\WifiRequest;
use App\Services\WifiRequestService;
use App\Support\MacAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WifiRequestServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_normalize_mac_lower_com_dois_pontos(): void
    {
        $this->assertSame('aa:bb:cc:dd:ee:ff', MacAddress::normalize('AA:BB:CC:DD:EE:FF'));
        $this->assertSame('aa:bb:cc:dd:ee:ff', MacAddress::normalize('AA-BB-CC-DD-EE-FF'));
        $this->assertSame('aa:bb:cc:dd:ee:ff', MacAddress::normalize('AABBCCDDEEFF'));
    }

    public function test_is_valid_mac(): void
    {
        $this->assertTrue(MacAddress::isValid('AA:BB:CC:DD:EE:FF'));
        $this->assertTrue(MacAddress::isValid('aa-bb-cc-dd-ee-ff'));
        $this->assertFalse(MacAddress::isValid('nao-mac'));
        $this->assertFalse(MacAddress::isValid('AA:BB:CC:DD:EE'));
    }

    public function test_solicitar_acesso_cria_visitor_e_wifi_request_com_mac_normalizado(): void
    {
        $service = app(WifiRequestService::class);

        $wifiRequest = $service->solicitarAcesso(
            dadosVisitante: [
                'name' => 'Maria',
                'email' => 'maria@x.com',
                'document' => '123',
                'phone' => null,
                'client_mac' => 'AA:BB:CC:DD:EE:FF',
            ],
            motivo: 'Motivo'
        );

        $this->assertNotNull($wifiRequest->id);
        $this->assertSame(WifiRequestStatus::PENDING, $wifiRequest->status);
        $this->assertSame('aa:bb:cc:dd:ee:ff', $wifiRequest->visitor->client_mac);
    }

    public function test_update_or_create_atualiza_mac_quando_email_mesmo(): void
    {
        $service = app(WifiRequestService::class);

        $service->solicitarAcesso(
            dadosVisitante: [
                'name' => 'Maria',
                'email' => 'maria@x.com',
                'document' => '123',
                'phone' => null,
                'client_mac' => 'AA:BB:CC:DD:EE:FF',
            ],
            motivo: 'Motivo 1'
        );

        $service->solicitarAcesso(
            dadosVisitante: [
                'name' => 'Maria',
                'email' => 'maria@x.com',
                'document' => '123',
                'phone' => null,
                'client_mac' => '11:22:33:44:55:66',
            ],
            motivo: 'Motivo 2'
        );

        $visitor = Visitor::where('email', 'maria@x.com')->first();
        $this->assertNotNull($visitor);
        $this->assertSame('11:22:33:44:55:66', $visitor->client_mac);
        $this->assertCount(2, $visitor->wifiRequests);
    }

    public function test_expirar_aprovados_transita_apenas_expirados(): void
    {
        $service = app(WifiRequestService::class);

        $visitor = Visitor::factory()->create(['client_mac' => 'aa:bb:cc:dd:ee:ff']);

        WifiRequest::factory()->create([
            'visitor_id' => $visitor->id,
            'status' => WifiRequestStatus::APPROVED,
            'expires_at' => now()->subHour(),
        ]);

        WifiRequest::factory()->create([
            'visitor_id' => $visitor->id,
            'status' => WifiRequestStatus::APPROVED,
            'expires_at' => now()->addHour(),
        ]);

        $total = $service->expirarAprovados();

        $this->assertSame(1, $total);
        $this->assertSame(
            1,
            WifiRequest::where('status', WifiRequestStatus::EXPIRED)->count()
        );
    }

    public function test_latest_status_for_mac_retorna_ultimo_pedido(): void
    {
        $service = app(WifiRequestService::class);

        $visitor = Visitor::factory()->create(['client_mac' => 'aa:bb:cc:dd:ee:ff']);

        $old = WifiRequest::factory()->create([
            'visitor_id' => $visitor->id,
            'status' => WifiRequestStatus::REJECTED,
        ]);
        $old->created_at = now()->subDay();
        $old->save();

        WifiRequest::factory()->create([
            'visitor_id' => $visitor->id,
            'status' => WifiRequestStatus::APPROVED,
            'expires_at' => now()->addHour(),
        ]);

        $latest = $service->latestStatusForMac('AA:BB:CC:DD:EE:FF');

        $this->assertNotNull($latest);
        $this->assertSame(WifiRequestStatus::APPROVED, $latest->status);
    }
}
