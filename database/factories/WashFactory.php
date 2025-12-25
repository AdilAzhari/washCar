<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Bay;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Wash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wash>
 */
final class WashFactory extends Factory
{
    protected $model = Wash::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $branch = Branch::factory()->create();
        $package = Package::factory()->create();

        return [
            'branch_id' => $branch->id,
            'bay_id' => Bay::factory()->create(['branch_id' => $branch->id]),
            'customer_id' => Customer::factory()->create(),
            'package_id' => $package->id,
            'total_amount' => $package->price,
            'progress' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['active', 'completed', 'cancelled']),
            'started_at' => now()->subHours($this->faker->numberBetween(1, 5)),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'active',
            'started_at' => now()->subHours($this->faker->numberBetween(1, 3)),
            'completed_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'completed',
            'progress' => 100,
            'started_at' => now()->subHours(2),
            'completed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'cancelled',
            'completed_at' => null,
        ]);
    }
}
