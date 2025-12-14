<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $notifications = [
            [
                'type' => 'info',
                'title' => 'System Update',
                'message' => 'The system has been updated to version 2.5.0 with new features and improvements.',
                'is_read' => true,
                'created_at' => now()->subDays(5),
            ],
            [
                'type' => 'warning',
                'title' => 'Low Inventory Alert',
                'message' => 'Tire Shine is running low at Downtown branch. Current stock: 8 bottles.',
                'is_read' => false,
                'created_at' => now()->subDays(2),
            ],
            [
                'type' => 'success',
                'title' => 'New Staff Member',
                'message' => 'Jane Smith has been added to the system as a staff member at Uptown branch.',
                'is_read' => false,
                'created_at' => now()->subDays(1),
            ],
            [
                'type' => 'error',
                'title' => 'Bay Maintenance Required',
                'message' => 'Bay 3 at Downtown branch requires immediate maintenance. Equipment malfunction detected.',
                'is_read' => false,
                'created_at' => now()->subHours(12),
            ],
            [
                'type' => 'info',
                'title' => 'Daily Revenue Report',
                'message' => 'Yesterday\'s total revenue: $1,245.50 across all branches.',
                'is_read' => true,
                'created_at' => now()->subHours(8),
            ],
            [
                'type' => 'warning',
                'title' => 'Queue Backup',
                'message' => 'Long queue detected at Midtown branch. Consider allocating additional staff.',
                'is_read' => false,
                'created_at' => now()->subHours(3),
            ],
            [
                'type' => 'success',
                'title' => 'Package Updated',
                'message' => 'Premium Wash package pricing has been updated successfully.',
                'is_read' => true,
                'created_at' => now()->subHours(2),
            ],
            [
                'type' => 'info',
                'title' => 'New Customer Registration',
                'message' => '5 new customers have registered today.',
                'is_read' => false,
                'created_at' => now()->subHour(),
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create([
                'user_id' => $users->random()->id,
                'type' => $notification['type'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'is_read' => $notification['is_read'],
                'read_at' => $notification['is_read'] ? $notification['created_at']->addMinutes(rand(10, 60)) : null,
                'created_at' => $notification['created_at'],
                'updated_at' => $notification['created_at'],
            ]);
        }
    }
}
