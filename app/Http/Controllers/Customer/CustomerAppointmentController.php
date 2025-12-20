<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Package;
use App\Notifications\AppointmentConfirmed;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerAppointmentController extends Controller
{
    /**
     * Display a listing of customer's appointments.
     */
    public function index(Request $request): Response
    {
        $customer = $request->user();

        $appointments = $customer->appointments()
            ->with(['branch', 'package', 'assignedStaff'])
            ->orderBy('scheduled_at', 'desc')
            ->paginate(15);

        return Inertia::render('Customer/Appointments/Index', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create(Request $request): Response
    {
        // Get active branches
        $branches = Branch::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get all packages
        $packages = Package::orderBy('name')->get();

        return Inertia::render('Customer/Appointments/Create', [
            'branches' => $branches,
            'packages' => $packages,
        ]);
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'package_id' => 'required|exists:packages,id',
            'scheduled_at' => 'required|date|after:now',
            'plate_number' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:50',
            'special_requests' => 'nullable|string|max:500',
        ]);

        $customer = $request->user();

        $appointment = Appointment::create([
            'customer_id' => $customer->id,
            'branch_id' => $validated['branch_id'],
            'package_id' => $validated['package_id'],
            'scheduled_at' => $validated['scheduled_at'],
            'plate_number' => $validated['plate_number'],
            'vehicle_type' => $validated['vehicle_type'],
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending',
        ]);

        // Award bonus points for booking appointment
        $loyaltyPoints = $customer->loyaltyPoints;
        if ($loyaltyPoints) {
            $loyaltyPoints->addPoints(
                50,
                'Bonus for booking appointment',
                null,
                $appointment->id
            );
        }

        // Send confirmation notification
        $customer->notify(new AppointmentConfirmed($appointment));

        // Schedule reminder notifications
        $scheduledAt = Carbon::parse($validated['scheduled_at']);

        // 24 hours before reminder
        $reminder24h = $scheduledAt->copy()->subHours(24);
        if ($reminder24h->isFuture()) {
            SendAppointmentReminder::dispatch($appointment, '24h_before')
                ->delay($reminder24h);
        }

        // 1 hour before reminder
        $reminder1h = $scheduledAt->copy()->subHour();
        if ($reminder1h->isFuture()) {
            SendAppointmentReminder::dispatch($appointment, '1h_before')
                ->delay($reminder1h);
        }

        return redirect()
            ->route('customer.appointments.show', $appointment)
            ->with('success', 'Appointment booked successfully! You earned 50 bonus points.');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment): Response
    {
        $this->authorize('view', $appointment);

        $appointment->load(['branch', 'package', 'assignedStaff', 'queueEntry']);

        return Inertia::render('Customer/Appointments/Show', [
            'appointment' => $appointment,
        ]);
    }

    /**
     * Cancel (delete) the specified appointment.
     */
    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);

        if (! $appointment->canBeCancelled()) {
            return back()->withErrors(['appointment' => 'This appointment cannot be cancelled.']);
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()
            ->route('customer.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }
}
