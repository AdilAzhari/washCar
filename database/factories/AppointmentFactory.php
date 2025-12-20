<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'customer_id' => User::factory()->create(['role' => 'customer']),
            'package_id' => Package::factory(),
            'plate_number' => strtoupper($this->faker->bothify('???###')),
            'vehicle_type' => $this->faker->randomElement(['Sedan', 'SUV', 'Truck', 'Van']),
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+30 days'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled', 'no_show']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'actual_start_at' => null,
            'completed_at' => null,
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'actual_start_at' => null,
            'completed_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'actual_start_at' => now()->subHours(2),
            'completed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    public function noShow(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'no_show',
        ]);
    }
}
