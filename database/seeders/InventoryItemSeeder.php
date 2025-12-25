<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

final class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            return;
        }

        $items = [
            ['name' => 'Car Shampoo', 'category' => 'Cleaning Supplies', 'quantity' => 25, 'min_quantity' => 10, 'unit' => 'bottles', 'unit_price' => 12.50],
            ['name' => 'Microfiber Towels', 'category' => 'Equipment', 'quantity' => 50, 'min_quantity' => 20, 'unit' => 'pieces', 'unit_price' => 3.50],
            ['name' => 'Tire Shine', 'category' => 'Cleaning Supplies', 'quantity' => 8, 'min_quantity' => 15, 'unit' => 'bottles', 'unit_price' => 8.99],
            ['name' => 'Glass Cleaner', 'category' => 'Cleaning Supplies', 'quantity' => 18, 'min_quantity' => 10, 'unit' => 'bottles', 'unit_price' => 6.75],
            ['name' => 'Wax Polish', 'category' => 'Detailing', 'quantity' => 12, 'min_quantity' => 8, 'unit' => 'bottles', 'unit_price' => 15.99],
            ['name' => 'Vacuum Bags', 'category' => 'Equipment', 'quantity' => 30, 'min_quantity' => 15, 'unit' => 'pieces', 'unit_price' => 2.25],
            ['name' => 'Wheel Cleaner', 'category' => 'Cleaning Supplies', 'quantity' => 14, 'min_quantity' => 10, 'unit' => 'bottles', 'unit_price' => 9.50],
            ['name' => 'Air Freshener', 'category' => 'Detailing', 'quantity' => 40, 'min_quantity' => 25, 'unit' => 'units', 'unit_price' => 4.25],
            ['name' => 'Foam Applicators', 'category' => 'Equipment', 'quantity' => 5, 'min_quantity' => 20, 'unit' => 'pieces', 'unit_price' => 1.75],
            ['name' => 'Spray Bottles', 'category' => 'Equipment', 'quantity' => 22, 'min_quantity' => 12, 'unit' => 'pieces', 'unit_price' => 3.99],
        ];

        foreach ($branches as $branch) {
            foreach ($items as $item) {
                InventoryItem::create([
                    'branch_id' => $branch->id,
                    'name' => $item['name'],
                    'category' => $item['category'],
                    'quantity' => $item['quantity'] + random_int(-5, 10),
                    'min_quantity' => $item['min_quantity'],
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'notes' => random_int(0, 1) !== 0 ? 'Regular stock item' : null,
                ]);
            }
        }
    }
}
