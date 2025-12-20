<?php

namespace Database\Factories;

use App\Models\Bay;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bay>
 */
class BayFactory extends Factory
{
    protected $model = Bay::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'name' => 'Bay ' . $this->faker->numberBetween(1, 20),
            'status' => $this->faker->randomElement(['idle', 'active', 'maintenance']),
        ];
    }

    public function idle(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'idle',
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'maintenance',
        ]);
    }
}
