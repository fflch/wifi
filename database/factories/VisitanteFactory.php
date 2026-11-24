<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VisitanteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'documento' => fake()->numerify('###.###.###-##'),
            'telefone' => fake()->numerify('(11) 9####-####'),
            'mac' => fake()->unique()->macAddress(),
        ];
    }
}
