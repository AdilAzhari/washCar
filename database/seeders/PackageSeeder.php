<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic Wash',
                'description' => 'Quick exterior wash with soap and rinse',
                'price' => 15.00,
                'duration_minutes' => 15,
                'color' => '#3b82f6',
                'is_active' => true,
            ],
            [
                'name' => 'Standard Wash',
                'description' => 'Exterior wash with wax and tire shine',
                'price' => 25.00,
                'duration_minutes' => 25,
                'color' => '#10b981',
                'is_active' => true,
            ],
            [
                'name' => 'Deluxe Wash',
                'description' => 'Complete wash with interior vacuum and detail',
                'price' => 40.00,
                'duration_minutes' => 45,
                'color' => '#f59e0b',
                'is_active' => true,
            ],
            [
                'name' => 'Premium Wash',
                'description' => 'Full service with hand wash, wax, interior detail, and polish',
                'price' => 60.00,
                'duration_minutes' => 60,
                'color' => '#a855f7',
                'is_active' => true,
            ],
            [
                'name' => 'Ultimate Detail',
                'description' => 'Complete detail service with clay bar, polish, and interior deep clean',
                'price' => 120.00,
                'duration_minutes' => 120,
                'color' => '#ef4444',
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            \App\Models\Package::create($package);
        }
    }
}
