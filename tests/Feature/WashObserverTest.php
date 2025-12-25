<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Bay;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Package;
use App\Models\User;
use App\Models\Wash;
use App\Notifications\WashCompleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class WashObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_loyalty_points_awarded_when_wash_completed_for_registered_user(): void
    {
        Notification::fake();

        // Create a user (customer account)
        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        // Create a branch
        $branch = Branch::factory()->create([
            'code' => 'TEST001',
        ]);

        // Create a bay
        $bay = Bay::factory()->create([
            'branch_id' => $branch->id,
            'name' => 'Bay 1',
            'status' => 'idle',
        ]);

        // Create a package
        $package = Package::factory()->create([
            'name' => 'Premium Wash',
            'description' => 'Premium car wash',
            'duration_minutes' => 45,
            'price' => 50.00,
        ]);

        // Create a customer linked to the user
        $customer = Customer::create([
            'name' => 'Jane Doe',
            'phone' => '555-5678',
            'email' => 'jane@example.com',
            'plate_number' => 'DEF456',
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        // Create a wash in progress
        $wash = Wash::create([
            'branch_id' => $branch->id,
            'bay_id' => $bay->id,
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Complete the wash
        $wash->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Assert notification was sent to user (not customer)
        Notification::assertSentTo(
            [$user],
            WashCompleted::class
        );

        // Check that loyalty points were created
        $this->assertDatabaseHas('loyalty_points', [
            'customer_id' => $user->id,
        ]);
    }

    public function test_anonymous_customer_wash_completion_does_not_crash(): void
    {
        // Create a branch
        $branch = Branch::factory()->create([
            'code' => 'TEST002',
        ]);

        // Create a bay
        $bay = Bay::factory()->create([
            'branch_id' => $branch->id,
            'name' => 'Bay 1',
            'status' => 'idle',
        ]);

        // Create a package
        $package = Package::factory()->create([
            'name' => 'Basic Wash',
            'description' => 'Basic car wash',
            'duration_minutes' => 30,
            'price' => 25.00,
        ]);

        // Create a customer without a user account (walk-in)
        $customer = Customer::create([
            'name' => 'Walk-in Customer',
            'phone' => '555-9999',
            'plate_number' => 'GHI789',
            'status' => 'active',
            'user_id' => null,
        ]);

        // Create a wash in progress
        $wash = Wash::create([
            'branch_id' => $branch->id,
            'bay_id' => $bay->id,
            'customer_id' => $customer->id,
            'package_id' => $package->id,
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Complete the wash - should not crash even though customer has no user account
        $wash->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Assert we didn't crash
        $this->assertTrue(true);

        // Assert no loyalty points were created
        $this->assertDatabaseMissing('loyalty_points', [
            'customer_id' => $customer->id,
        ]);
    }
}
