<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
final class PackageFactory extends Factory
{
    protected $model = Package::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $packages = [
            ['name' => 'Basic Wash', 'price' => 25, 'duration' => 30, 'color' => 'blue'],
            ['name' => 'Premium Wash', 'price' => 45, 'duration' => 45, 'color' => 'purple'],
            ['name' => 'Deluxe Wash', 'price' => 65, 'duration' => 60, 'color' => 'gold'],
            ['name' => 'Ultimate Wash', 'price' => 95, 'duration' => 90, 'color' => 'red'],
        ];

        $package = $this->faker->randomElement($packages);

        return [
            'name' => $package['name'],
            'description' => $this->faker->sentence(),
            'price' => $package['price'],
            'duration_minutes' => $package['duration'],
            'color' => $package['color'],
            'is_active' => true,
            'loyalty_points' => $package['price'] * 10,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
