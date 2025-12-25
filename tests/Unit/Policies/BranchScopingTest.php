<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Appointment;
use App\Models\Bay;
use App\Models\Branch;
use App\Models\QueueEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BranchScopingTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branchA;

    protected Branch $branchB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branchA = Branch::factory()->create(['name' => 'Branch A']);
        $this->branchB = Branch::factory()->create(['name' => 'Branch B']);
    }

    public function test_manager_can_view_their_branch(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertTrue($manager->can('view', $this->branchA));
    }

    public function test_manager_cannot_view_other_branch(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertFalse($manager->can('view', $this->branchB));
    }

    public function test_staff_can_view_their_branch(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertTrue($staff->can('view', $this->branchA));
    }

    public function test_staff_cannot_view_other_branch(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertFalse($staff->can('view', $this->branchB));
    }

    public function test_admin_can_view_any_branch(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($admin->can('view', $this->branchA));
        $this->assertTrue($admin->can('view', $this->branchB));
    }

    public function test_manager_can_view_appointments_in_their_branch(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branchA->id,
        ]);

        $appointment = Appointment::factory()->create([
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertTrue($manager->can('view', $appointment));
    }

    public function test_manager_cannot_view_appointments_in_other_branch(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branchA->id,
        ]);

        $appointment = Appointment::factory()->create([
            'branch_id' => $this->branchB->id,
        ]);

        $this->assertFalse($manager->can('view', $appointment));
    }

    public function test_staff_can_view_queue_entries_in_their_branch(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $this->branchA->id,
        ]);

        $queueEntry = QueueEntry::factory()->create([
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertTrue($staff->can('view', $queueEntry));
    }

    public function test_staff_cannot_view_queue_entries_in_other_branch(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $this->branchA->id,
        ]);

        $queueEntry = QueueEntry::factory()->create([
            'branch_id' => $this->branchB->id,
        ]);

        $this->assertFalse($staff->can('view', $queueEntry));
    }

    public function test_manager_can_manage_bays_in_their_branch(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branchA->id,
        ]);

        $bay = Bay::factory()->create([
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertTrue($manager->can('update', $bay));
    }

    public function test_manager_cannot_manage_bays_in_other_branch(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branchA->id,
        ]);

        $bay = Bay::factory()->create([
            'branch_id' => $this->branchB->id,
        ]);

        $this->assertFalse($manager->can('update', $bay));
    }

    public function test_customer_can_view_their_own_appointment(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_customer' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertTrue($customer->can('view', $appointment));
    }

    public function test_customer_cannot_view_other_customer_appointment(): void
    {
        $customer1 = User::factory()->create([
            'role' => 'customer',
            'is_customer' => true,
        ]);

        $customer2 = User::factory()->create([
            'role' => 'customer',
            'is_customer' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'customer_id' => $customer2->id,
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertFalse($customer1->can('view', $appointment));
    }

    public function test_staff_cannot_update_branch_details(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertFalse($staff->can('update', $this->branchA));
    }

    public function test_manager_can_update_their_branch_details(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertTrue($manager->can('update', $this->branchA));
    }

    public function test_only_admin_can_delete_branch(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branchA->id,
        ]);

        $this->assertTrue($admin->can('delete', $this->branchA));
        $this->assertFalse($manager->can('delete', $this->branchA));
    }
}
