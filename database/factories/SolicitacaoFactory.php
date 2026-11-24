<?php

namespace Database\Factories;

use App\Models\Solicitacao;
use App\Models\Visitante;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitacaoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'visitante_id' => Visitante::factory(),
            'motivo' => fake()->sentence(8),
        ];
    }

    /**
     * Toda solicitação nasce pendente
     */
    public function configure(): static
    {
        return $this->afterCreating(fn (Solicitacao $solicitacao) => $solicitacao->setStatus('pendente'));
    }
}
