<?php declare(strict_types=1);

namespace Database\Factories;

use App\Enums\WifiRequestStatus;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

class WifiRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'visitor_id' => Visitor::factory(),
            'reason' => fake()->sentence(6),
            'status' => WifiRequestStatus::PENDING,
        ];
    }
}
