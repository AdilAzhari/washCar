<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Downtown Branch',
                'code' => 'DWN',
                'address' => '123 Main Street, Downtown',
                'phone' => '+1 (555) 123-4567',
                'operating_hours' => '8:00 AM - 8:00 PM',
                'manager_name' => 'John Manager',
                'manager_contact' => '+1 (555) 123-4568',
                'opening_time' => '08:00',
                'closing_time' => '20:00',
                'is_active' => true,
            ],
            [
                'name' => 'Northside Branch',
                'code' => 'NTH',
                'address' => '456 North Avenue, Northside',
                'phone' => '+1 (555) 234-5678',
                'operating_hours' => '7:00 AM - 9:00 PM',
                'manager_name' => 'Sarah Smith',
                'manager_contact' => '+1 (555) 234-5679',
                'opening_time' => '07:00',
                'closing_time' => '21:00',
                'is_active' => true,
            ],
            [
                'name' => 'Eastside Branch',
                'code' => 'EST',
                'address' => '789 East Boulevard, Eastside',
                'phone' => '+1 (555) 345-6789',
                'operating_hours' => '8:00 AM - 7:00 PM',
                'manager_name' => 'Mike Johnson',
                'manager_contact' => '+1 (555) 345-6790',
                'opening_time' => '08:00',
                'closing_time' => '19:00',
                'is_active' => true,
            ],
            [
                'name' => 'Westside Branch',
                'code' => 'WST',
                'address' => '321 West Road, Westside',
                'phone' => '+1 (555) 456-7890',
                'operating_hours' => '9:00 AM - 6:00 PM',
                'manager_name' => 'Emily Brown',
                'manager_contact' => '+1 (555) 456-7891',
                'opening_time' => '09:00',
                'closing_time' => '18:00',
                'is_active' => false,
            ],
        ];

        foreach ($branches as $branch) {
            \App\Models\Branch::create($branch);
        }
    }
}
