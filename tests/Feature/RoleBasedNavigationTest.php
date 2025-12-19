<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test branch for staff and managers
        $this->branch = Branch::create([
            'name' => 'Test Branch',
            'code' => 'TB001',
            'address' => '123 Test St',
            'phone' => '1234567890',
            'active' => true,
        ]);
    }

    public function test_admin_can_access_dashboard_and_navigate(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin);

        // Test dashboard access
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));

        // Test navigation to various admin routes
        $adminRoutes = [
            'admin.branches.index',
            'admin.bays.index',
            'admin.queue.index',
            'admin.customers.index',
            'admin.packages.index',
            'admin.staff.index',
            'admin.inventory.index',
        ];

        foreach ($adminRoutes as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertStatus(200);
        }
    }

    public function test_manager_can_access_dashboard_and_navigate(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branch->id,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($manager);

        // Test dashboard access
        $response = $this->get(route('manager.dashboard'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Manager/Dashboard'));

        // Test navigation to manager-specific routes
        $managerRoutes = [
            'manager.reports',
            'manager.queue.index',
            'manager.queue.waiting',
            'manager.queue.in-progress',
            'manager.bays.index',
            'manager.customers.index',
            'manager.packages.index',
            'manager.staff.index',
            'manager.inventory.index',
        ];

        foreach ($managerRoutes as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertStatus(200);
        }
    }

    public function test_manager_cannot_access_admin_only_routes(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branch->id,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($manager);

        // Manager should not access admin dashboard
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        // Manager should not access branch management (admin only)
        $response = $this->get(route('admin.branches.index'));
        $response->assertStatus(403);
    }

    public function test_staff_can_access_dashboard_and_navigate(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $this->branch->id,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($staff);

        // Test dashboard access
        $response = $this->get(route('staff.dashboard'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Staff/Dashboard'));

        // Test navigation to staff-accessible routes
        $staffRoutes = [
            'staff.queue.index',
            'staff.bays.index',
            'staff.customers.index',
            'staff.inventory.index',
        ];

        foreach ($staffRoutes as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertStatus(200);
        }
    }

    public function test_staff_cannot_access_admin_or_manager_routes(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $this->branch->id,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($staff);

        // Staff should not access admin dashboard
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        // Staff should not access manager dashboard
        $response = $this->get(route('manager.dashboard'));
        $response->assertStatus(403);

        // Staff should not access branch management
        $response = $this->get(route('admin.branches.index'));
        $response->assertStatus(403);
    }

    public function test_customer_can_access_dashboard_and_navigate(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($customer);

        // Test dashboard access
        $response = $this->get(route('customer.dashboard'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Customer/Dashboard'));

        // Test navigation to customer routes
        $customerRoutes = [
            'customer.history',
            'customer.loyalty',
            'customer.appointments.index',
        ];

        foreach ($customerRoutes as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertStatus(200);
        }
    }

    public function test_customer_cannot_access_staff_manager_or_admin_routes(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($customer);

        // Customer should not access admin dashboard
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        // Customer should not access manager dashboard
        $response = $this->get(route('manager.dashboard'));
        $response->assertStatus(403);

        // Customer should not access staff dashboard
        $response = $this->get(route('staff.dashboard'));
        $response->assertStatus(403);
    }

    public function test_manager_without_branch_is_redirected_to_profile(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => null,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($manager);

        // Manager without branch should be redirected to profile with error
        $response = $this->get(route('manager.dashboard'));
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('error');
    }

    public function test_staff_without_branch_is_redirected_to_profile(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => null,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($staff);

        // Staff without branch should be redirected to profile with error
        $response = $this->get(route('staff.dashboard'));
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('error');
    }

    public function test_manager_can_navigate_through_queue_pages_with_correct_layout(): void
    {
        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $this->branch->id,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($manager);

        // Test that manager can access queue pages and they render correctly
        $queueRoutes = [
            'manager.queue.index',
            'manager.queue.waiting',
            'manager.queue.in-progress',
            'manager.queue.view',
        ];

        foreach ($queueRoutes as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertStatus(200);
            // Verify the page loads without errors
            $response->assertInertia(fn ($page) => $page->has('auth.user'));
        }
    }

    public function test_staff_can_navigate_through_allowed_queue_pages(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'branch_id' => $this->branch->id,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($staff);

        // Test that staff can access their allowed queue pages
        $response = $this->get(route('staff.queue.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('auth.user'));
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        // Test various routes redirect to login
        $routes = [
            route('admin.dashboard'),
            route('manager.dashboard'),
            route('staff.dashboard'),
            route('customer.dashboard'),
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $response->assertRedirect(route('login'));
        }
    }
}
