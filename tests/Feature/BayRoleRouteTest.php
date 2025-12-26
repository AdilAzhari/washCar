<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Bay;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BayRoleRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'code' => 'TB001',
            'address' => '123 Test St',
            'phone' => '1234567890',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_perform_bay_actions(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $bay = Bay::create(['name' => 'Bay 1', 'branch_id' => $this->branch->id, 'status' => 'idle']);

        $this->actingAs($admin);

        // Test Update Status
        $response = $this->patch(route('admin.bays.update-status', $bay), ['status' => 'active']);
        $response->assertRedirect();
        $this->assertEquals('active', $bay->fresh()->status);

        // Test Delete
        $response = $this->delete(route('admin.bays.destroy', $bay));
        $response->assertRedirect(route('admin.bays.index'));
        $this->assertDatabaseMissing('bays', ['id' => $bay->id]);
    }

    public function test_manager_can_perform_bay_actions(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branch->id,
            'email_verified_at' => now()
        ]);
        $bay = Bay::create(['name' => 'Bay 2', 'branch_id' => $this->branch->id, 'status' => 'idle']);

        $this->actingAs($manager);

        // Test Update Status
        $response = $this->patch(route('manager.bays.update-status', $bay), ['status' => 'active']);
        $response->assertRedirect();
        $this->assertEquals('active', $bay->fresh()->status);

        // Test Delete
        $response = $this->delete(route('manager.bays.destroy', $bay));
        $response->assertRedirect(route('manager.bays.index'));
        $this->assertDatabaseMissing('bays', ['id' => $bay->id]);
    }

    public function test_staff_can_update_bay_status(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $this->branch->id,
            'email_verified_at' => now()
        ]);
        $bay = Bay::create(['name' => 'Bay 3', 'branch_id' => $this->branch->id, 'status' => 'idle']);

        $this->actingAs($staff);

        // Test Update Status
        $response = $this->patch(route('staff.bays.update-status', $bay), ['status' => 'active']);
        $response->assertRedirect();
        $this->assertEquals('active', $bay->fresh()->status);

        // Staff does NOT have a destroy route defined or should not access it
        // According to routes/web.php, staff.bays only has index and update (not destroy)
        $response = $this->delete(route('admin.bays.destroy', $bay));
        $response->assertStatus(403);
    }
}
