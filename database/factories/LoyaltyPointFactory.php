<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\LoyaltyPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoyaltyPoint>
 */
class LoyaltyPointFactory extends Factory
{
    protected $model = LoyaltyPoint::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'points' => $this->faker->numberBetween(0, 5000),
            'tier' => $this->faker->randomElement(['bronze', 'silver', 'gold', 'platinum']),
        ];
    }

    public function bronze(): static
    {
        return $this->state(fn (array $attributes) => [
            'points' => $this->faker->numberBetween(0, 499),
            'tier' => 'bronze',
        ]);
    }

    public function silver(): static
    {
        return $this->state(fn (array $attributes) => [
            'points' => $this->faker->numberBetween(500, 1499),
            'tier' => 'silver',
        ]);
    }

    public function gold(): static
    {
        return $this->state(fn (array $attributes) => [
            'points' => $this->faker->numberBetween(1500, 2999),
            'tier' => 'gold',
        ]);
    }

    public function platinum(): static
    {
        return $this->state(fn (array $attributes) => [
            'points' => $this->faker->numberBetween(3000, 10000),
            'tier' => 'platinum',
        ]);
    }
}
