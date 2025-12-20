<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Package;
use App\Models\QueueEntry;
use App\Notifications\QueuePositionUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class QueueEntryObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_receives_notification_when_near_front_of_queue(): void
    {
        Notification::fake();

        // Create a branch
        $branch = Branch::factory()->create([
            'code' => 'TEST001',
        ]);

        // Create a package
        $package = Package::factory()->create([
            'name' => 'Basic Wash',
            'description' => 'Basic car wash',
            'duration_minutes' => 30,
            'price' => 25.00,
        ]);

        // Create a customer with Notifiable trait
        $customer = Customer::create([
            'name' => 'John Doe',
            'phone' => '555-1234',
            'email' => 'john@example.com',
            'plate_number' => 'ABC123',
            'status' => 'active',
        ]);

        // Create a queue entry at position 3
        $queueEntry = QueueEntry::create([
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'plate_number' => 'ABC123',
            'position' => 3,
            'status' => 'waiting',
            'joined_at' => now(),
        ]);

        // Update position to 2 (should trigger notification)
        $queueEntry->update(['position' => 2]);

        // Assert notification was sent
        Notification::assertSentTo(
            [$customer],
            QueuePositionUpdate::class
        );
    }

    public function test_anonymous_customer_does_not_crash_when_notifying(): void
    {
        // Create a branch
        $branch = Branch::factory()->create([
            'code' => 'TEST002',
        ]);

        // Create a queue entry without a customer (walk-in)
        $queueEntry = QueueEntry::create([
            'branch_id' => $branch->id,
            'customer_id' => null,
            'package_id' => null,
            'plate_number' => 'XYZ789',
            'position' => 5,
            'status' => 'waiting',
            'joined_at' => now(),
        ]);

        // Update position - should not crash
        $queueEntry->update(['position' => 2]);

        // Assert we didn't crash
        $this->assertTrue(true);
    }
}
