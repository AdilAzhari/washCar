<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'John Smith',
                'phone' => '+1 (555) 111-2222',
                'email' => 'john.smith@email.com',
                'plate_number' => 'ABC-1234',
                'vehicle_type' => 'sedan',
                'make' => 'Toyota',
                'model' => 'Camry',
                'color' => 'Silver',
                'membership' => 'Gold',
                'status' => 'active',
            ],
            [
                'name' => 'Sarah Johnson',
                'phone' => '+1 (555) 222-3333',
                'email' => 'sarah.j@email.com',
                'plate_number' => 'XYZ-5678',
                'vehicle_type' => 'suv',
                'make' => 'Honda',
                'model' => 'CR-V',
                'color' => 'White',
                'membership' => 'Platinum',
                'status' => 'active',
            ],
            [
                'name' => 'Michael Brown',
                'phone' => '+1 (555) 333-4444',
                'email' => 'mbrown@email.com',
                'plate_number' => 'DEF-9012',
                'vehicle_type' => 'truck',
                'make' => 'Ford',
                'model' => 'F-150',
                'color' => 'Black',
                'membership' => 'Silver',
                'status' => 'active',
            ],
            [
                'name' => 'Emily Davis',
                'phone' => '+1 (555) 444-5555',
                'email' => 'emily.davis@email.com',
                'plate_number' => 'GHI-3456',
                'vehicle_type' => 'sedan',
                'make' => 'Nissan',
                'model' => 'Altima',
                'color' => 'Blue',
                'membership' => 'Regular',
                'status' => 'active',
            ],
            [
                'name' => 'David Wilson',
                'phone' => '+1 (555) 555-6666',
                'email' => null,
                'plate_number' => 'JKL-7890',
                'vehicle_type' => 'van',
                'make' => 'Chrysler',
                'model' => 'Pacifica',
                'color' => 'Red',
                'membership' => 'Regular',
                'status' => 'active',
            ],
            [
                'name' => 'Jessica Martinez',
                'phone' => '+1 (555) 666-7777',
                'email' => 'jmartinez@email.com',
                'plate_number' => 'MNO-2345',
                'vehicle_type' => 'suv',
                'make' => 'Mazda',
                'model' => 'CX-5',
                'color' => 'Gray',
                'membership' => 'Gold',
                'status' => 'active',
            ],
            [
                'name' => 'Robert Taylor',
                'phone' => '+1 (555) 777-8888',
                'email' => 'rtaylor@email.com',
                'plate_number' => 'PQR-6789',
                'vehicle_type' => 'sedan',
                'make' => 'BMW',
                'model' => '330i',
                'color' => 'White',
                'membership' => 'Platinum',
                'status' => 'active',
            ],
            [
                'name' => 'Lisa Anderson',
                'phone' => '+1 (555) 888-9999',
                'email' => null,
                'plate_number' => 'STU-0123',
                'vehicle_type' => 'motorcycle',
                'make' => 'Harley Davidson',
                'model' => 'Street 750',
                'color' => 'Black',
                'membership' => 'Regular',
                'status' => 'inactive',
            ],
        ];

        foreach ($customers as $customer) {
            \App\Models\Customer::create($customer);
        }
    }
}
