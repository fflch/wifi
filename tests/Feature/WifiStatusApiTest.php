<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\WifiRequestStatus;
use App\Models\Visitor;
use App\Models\WifiRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WifiStatusApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_rota_requer_token(): void
    {
        config(['wifi.controller_token' => 'secret-token']);

        $response = $this->getJson('/api/wifi/mac/aa:bb:cc:dd:ee:ff');

        $response->assertStatus(401);
        $response->assertJson(['error' => 'unauthorized']);
    }

    public function test_status_rota_sem_token_configurado_retorna_503(): void
    {
        config(['wifi.controller_token' => '']);

        $response = $this->getJson('/api/wifi/mac/aa:bb:cc:dd:ee:ff');

        $response->assertStatus(503);
        $response->assertJson(['error' => 'controller_token_not_configured']);
    }

    public function test_status_retorna_unknown_para_mac_nao_cadastrado(): void
    {
        config(['wifi.controller_token' => 'secret-token']);

        $response = $this->withHeader('X-Controller-Token', 'secret-token')
            ->getJson('/api/wifi/mac/aa:bb:cc:dd:ee:ff');

        $response->assertStatus(404);
        $response->assertJson(['status' => 'unknown']);
    }

    public function test_status_rejeita_mac_invalido(): void
    {
        config(['wifi.controller_token' => 'secret-token']);

        $response = $this->withHeader('X-Controller-Token', 'secret-token')
            ->getJson('/api/wifi/mac/nao-e-um-mac');

        $response->assertStatus(400);
        $response->assertJson(['error' => 'invalid_mac']);
    }

    public function test_status_retorna_pending_para_solicitacao_pendente(): void
    {
        config(['wifi.controller_token' => 'secret-token']);

        $visitor = Visitor::factory()->create(['client_mac' => 'aa:bb:cc:dd:ee:ff']);
        WifiRequest::factory()->create([
            'visitor_id' => $visitor->id,
            'status' => WifiRequestStatus::PENDING,
        ]);

        $response = $this->withHeader('X-Controller-Token', 'secret-token')
            ->getJson('/api/wifi/mac/AA:BB:CC:DD:EE:FF');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'pending']);
    }

    public function test_aprovados_lista_apenas_ativos(): void
    {
        config(['wifi.controller_token' => 'secret-token']);

        $visitor = Visitor::factory()->create(['client_mac' => 'aa:bb:cc:dd:ee:ff']);
        WifiRequest::factory()->create([
            'visitor_id' => $visitor->id,
            'status' => WifiRequestStatus::APPROVED,
            'expires_at' => now()->addHours(2),
        ]);

        $visitor2 = Visitor::factory()->create(['client_mac' => '11:22:33:44:55:66']);
        WifiRequest::factory()->create([
            'visitor_id' => $visitor2->id,
            'status' => WifiRequestStatus::APPROVED,
            'expires_at' => now()->subHour(),
        ]);

        $response = $this->withHeader('X-Controller-Token', 'secret-token')
            ->getJson('/api/wifi/aprovados');

        $response->assertStatus(200);
        $response->assertJsonPath('count', 1);
        $response->assertJsonPath('macs.0.mac', 'aa:bb:cc:dd:ee:ff');
    }

    public function test_fila_lista_apenas_pendentes(): void
    {
        config(['wifi.controller_token' => 'secret-token']);

        $visitor = Visitor::factory()->create(['client_mac' => 'aa:bb:cc:dd:ee:ff']);
        WifiRequest::factory()->create([
            'visitor_id' => $visitor->id,
            'status' => WifiRequestStatus::PENDING,
        ]);

        $visitor2 = Visitor::factory()->create(['client_mac' => '11:22:33:44:55:66']);
        WifiRequest::factory()->create([
            'visitor_id' => $visitor2->id,
            'status' => WifiRequestStatus::APPROVED,
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->withHeader('X-Controller-Token', 'secret-token')
            ->getJson('/api/wifi/fila');

        $response->assertStatus(200);
        $response->assertJsonPath('count', 1);
        $response->assertJsonPath('macs.0.mac', 'aa:bb:cc:dd:ee:ff');
    }

    public function test_token_errado_retorna_401(): void
    {
        config(['wifi.controller_token' => 'secret-token']);

        $response = $this->withHeader('X-Controller-Token', 'errado')
            ->getJson('/api/wifi/mac/aa:bb:cc:dd:ee:ff');

        $response->assertStatus(401);
    }
}
