<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a branch for testing
        $this->branch = Branch::factory()->create();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_manager_cannot_access_admin_dashboard(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($manager)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_admin_dashboard(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($staff)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_customer' => true,
        ]);

        $response = $this->actingAs($customer)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_manager_can_access_manager_dashboard(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($manager)->get(route('manager.dashboard'));

        $response->assertStatus(200);
    }

    public function test_staff_can_access_staff_dashboard(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));

        $response->assertStatus(200);
    }

    public function test_customer_can_access_customer_dashboard(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'is_customer' => true,
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
    }

    public function test_manager_without_branch_cannot_access_dashboard(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => null,
        ]);

        $response = $this->actingAs($manager)->get(route('manager.dashboard'));

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('error');
    }

    public function test_staff_without_branch_cannot_access_dashboard(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => null,
        ]);

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('error');
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get(route('customer.dashboard'));

        $response->assertRedirect(route('login'));
    }
}
