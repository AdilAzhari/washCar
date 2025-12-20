<?php

namespace Tests\Feature\Customer;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\LoyaltyPoint;
use App\Models\Package;
use App\Models\User;
use App\Notifications\AppointmentConfirmed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Branch $branch;
    protected Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->package = Package::factory()->create([
            'loyalty_points' => 50,
        ]);
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'is_customer' => true,
        ]);

        // Create loyalty points record
        LoyaltyPoint::factory()->create([
            'customer_id' => $this->customer->id,
            'points' => 0,
            'lifetime_points' => 0,
        ]);
    }

    public function test_customer_can_view_appointments_page(): void
    {
        $response = $this->actingAs($this->customer)
            ->get(route('customer.appointments.index'));

        $response->assertStatus(200);
    }

    public function test_customer_can_view_create_appointment_page(): void
    {
        $response = $this->actingAs($this->customer)
            ->get(route('customer.appointments.create'));

        $response->assertStatus(200)
            ->assertInertia(
                fn ($page) => $page
                    ->component('Customer/Appointments/Create')
                    ->has('branches')
                    ->has('packages')
            );
    }

    public function test_customer_can_create_appointment(): void
    {
        Notification::fake();

        $appointmentData = [
            'branch_id' => $this->branch->id,
            'package_id' => $this->package->id,
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i'),
            'plate_number' => 'ABC-1234',
            'vehicle_type' => 'Sedan',
            'special_requests' => 'Please use premium wax',
        ];

        $response = $this->actingAs($this->customer)
            ->from(route('customer.appointments.create'))
            ->post(route('customer.appointments.store'), $appointmentData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert appointment created
        $this->assertDatabaseHas('appointments', [
            'customer_id' => $this->customer->id,
            'branch_id' => $this->branch->id,
            'package_id' => $this->package->id,
            'plate_number' => 'ABC-1234',
            'status' => 'pending',
        ]);

        // Assert bonus points awarded
        $this->customer->loyaltyPoints->refresh();
        $this->assertEquals(50, $this->customer->loyaltyPoints->points);

        // Assert notification sent
        Notification::assertSentTo($this->customer, AppointmentConfirmed::class);
    }

    public function test_customer_cannot_create_appointment_in_past(): void
    {
        $appointmentData = [
            'branch_id' => $this->branch->id,
            'package_id' => $this->package->id,
            'scheduled_at' => now()->subHours(1)->format('Y-m-d H:i'),
            'plate_number' => 'ABC-1234',
            'vehicle_type' => 'Sedan',
        ];

        $response = $this->actingAs($this->customer)
            ->from(route('customer.appointments.create'))
            ->post(route('customer.appointments.store'), $appointmentData);

        $response->assertSessionHasErrors('scheduled_at');
    }

    public function test_customer_can_view_their_appointment(): void
    {
        $appointment = Appointment::factory()->create([
            'customer_id' => $this->customer->id,
            'branch_id' => $this->branch->id,
            'package_id' => $this->package->id,
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('customer.appointments.show', $appointment));

        $response->assertStatus(200);
    }

    public function test_customer_cannot_view_other_customer_appointment(): void
    {
        $otherCustomer = User::factory()->create([
            'role' => 'customer',
            'is_customer' => true,
        ]);

        $appointment = Appointment::factory()->create([
            'customer_id' => $otherCustomer->id,
            'branch_id' => $this->branch->id,
            'package_id' => $this->package->id,
        ]);

        $response = $this->actingAs($this->customer)
            ->get(route('customer.appointments.show', $appointment));

        $response->assertStatus(403);
    }

    public function test_customer_can_cancel_pending_appointment(): void
    {
        $appointment = Appointment::factory()->create([
            'customer_id' => $this->customer->id,
            'branch_id' => $this->branch->id,
            'package_id' => $this->package->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->customer)
            ->from(route('customer.appointments.show', $appointment))
            ->delete(route('customer.appointments.destroy', $appointment));

        $response->assertRedirect();

        $appointment->refresh();
        $this->assertEquals('cancelled', $appointment->status);
    }

    public function test_customer_cannot_cancel_completed_appointment(): void
    {
        $appointment = Appointment::factory()->create([
            'customer_id' => $this->customer->id,
            'branch_id' => $this->branch->id,
            'package_id' => $this->package->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->customer)
            ->from(route('customer.appointments.show', $appointment))
            ->delete(route('customer.appointments.destroy', $appointment));

        $response->assertSessionHasErrors();

        $appointment->refresh();
        $this->assertEquals('completed', $appointment->status);
    }

    public function test_appointment_requires_all_fields(): void
    {
        $response = $this->actingAs($this->customer)
            ->from(route('customer.appointments.create'))
            ->post(route('customer.appointments.store'), []);

        $response->assertSessionHasErrors([
            'branch_id',
            'package_id',
            'scheduled_at',
            'plate_number',
            'vehicle_type',
        ]);
    }
}
