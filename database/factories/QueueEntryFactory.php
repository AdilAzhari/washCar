<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Package;
use App\Models\QueueEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QueueEntry>
 */
class QueueEntryFactory extends Factory
{
    protected $model = QueueEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'customer_id' => Customer::factory(),
            'package_id' => Package::factory(),
            'plate_number' => strtoupper($this->faker->bothify('???###')),
            'position' => $this->faker->numberBetween(1, 20),
            'status' => $this->faker->randomElement(['waiting', 'in_progress', 'completed', 'cancelled']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid']),
            'joined_at' => now(),
        ];
    }

    public function waiting(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'waiting',
            'started_at' => null,
            'completed_at' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'started_at' => now()->subHours(1),
            'completed_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'started_at' => now()->subHours(2),
            'completed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
