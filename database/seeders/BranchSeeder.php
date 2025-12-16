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
                'name' => 'Downtown Express Wash',
                'code' => 'DWN',
                'address' => '1250 Main Street, Downtown, Los Angeles, CA 90012',
                'phone' => '+1 (213) 555-1001',
                'operating_hours' => 'Mon-Sat: 7:00 AM - 10:00 PM, Sun: 8:00 AM - 8:00 PM',
                'manager_name' => 'Robert Martinez',
                'manager_contact' => '+1 (213) 555-1002',
                'opening_time' => '07:00',
                'closing_time' => '22:00',
                'is_active' => true,
            ],
            [
                'name' => 'Northside Premium Car Spa',
                'code' => 'NTH',
                'address' => '4567 Ventura Boulevard, North Hollywood, CA 91602',
                'phone' => '+1 (818) 555-2001',
                'operating_hours' => 'Mon-Sun: 6:00 AM - 11:00 PM',
                'manager_name' => 'Sarah Thompson',
                'manager_contact' => '+1 (818) 555-2002',
                'opening_time' => '06:00',
                'closing_time' => '23:00',
                'is_active' => true,
            ],
            [
                'name' => 'Eastside Auto Detailing',
                'code' => 'EST',
                'address' => '789 East Olympic Boulevard, East LA, CA 90023',
                'phone' => '+1 (323) 555-3001',
                'operating_hours' => 'Mon-Fri: 7:00 AM - 9:00 PM, Sat-Sun: 8:00 AM - 8:00 PM',
                'manager_name' => 'Michael Chen',
                'manager_contact' => '+1 (323) 555-3002',
                'opening_time' => '07:00',
                'closing_time' => '21:00',
                'is_active' => true,
            ],
            [
                'name' => 'Westside Luxury Wash',
                'code' => 'WST',
                'address' => '321 Santa Monica Boulevard, West LA, CA 90025',
                'phone' => '+1 (310) 555-4001',
                'operating_hours' => 'Mon-Sun: 8:00 AM - 9:00 PM',
                'manager_name' => 'Emily Rodriguez',
                'manager_contact' => '+1 (310) 555-4002',
                'opening_time' => '08:00',
                'closing_time' => '21:00',
                'is_active' => true,
            ],
            [
                'name' => 'Beverly Hills Elite Wash',
                'code' => 'BHE',
                'address' => '9876 Wilshire Boulevard, Beverly Hills, CA 90210',
                'phone' => '+1 (310) 555-5001',
                'operating_hours' => 'Mon-Sun: 7:00 AM - 10:00 PM',
                'manager_name' => 'James Anderson',
                'manager_contact' => '+1 (310) 555-5002',
                'opening_time' => '07:00',
                'closing_time' => '22:00',
                'is_active' => true,
            ],
            [
                'name' => 'Santa Monica Beach Wash',
                'code' => 'SMB',
                'address' => '1500 Ocean Avenue, Santa Monica, CA 90401',
                'phone' => '+1 (310) 555-6001',
                'operating_hours' => 'Mon-Sun: 6:00 AM - 10:00 PM',
                'manager_name' => 'Jennifer Williams',
                'manager_contact' => '+1 (310) 555-6002',
                'opening_time' => '06:00',
                'closing_time' => '22:00',
                'is_active' => true,
            ],
            [
                'name' => 'Pasadena Auto Spa',
                'code' => 'PAS',
                'address' => '2340 Colorado Boulevard, Pasadena, CA 91107',
                'phone' => '+1 (626) 555-7001',
                'operating_hours' => 'Mon-Fri: 7:00 AM - 8:00 PM, Sat-Sun: 8:00 AM - 7:00 PM',
                'manager_name' => 'David Kim',
                'manager_contact' => '+1 (626) 555-7002',
                'opening_time' => '07:00',
                'closing_time' => '20:00',
                'is_active' => true,
            ],
            [
                'name' => 'Long Beach Express',
                'code' => 'LBX',
                'address' => '5678 Pacific Coast Highway, Long Beach, CA 90802',
                'phone' => '+1 (562) 555-8001',
                'operating_hours' => 'Temporarily Closed for Renovation',
                'manager_name' => 'Lisa Garcia',
                'manager_contact' => '+1 (562) 555-8002',
                'opening_time' => '08:00',
                'closing_time' => '18:00',
                'is_active' => false,
            ],
        ];

        foreach ($branches as $branch) {
            \App\Models\Branch::create($branch);
        }
    }
}
