<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\QueueEntry;
use App\Models\Wash;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Appointment::class);

        $user = $request->user();

        // Build query with branch scoping
        $query = Appointment::with(['customer', 'branch', 'package', 'assignedStaff']);

        // Apply branch scoping for staff and managers
        if ($user->isStaff() || $user->isManager()) {
            $query->where('branch_id', $user->branch_id);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by date if provided
        if ($request->has('date')) {
            $query->whereDate('scheduled_at', $request->input('date'));
        } else {
            // Default to today and future
            $query->where('scheduled_at', '>=', now()->startOfDay());
        }

        $appointments = $query->orderBy('scheduled_at')->paginate(20);

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'filters' => $request->only(['status', 'date']),
        ]);
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment): Response
    {
        $this->authorize('view', $appointment);

        $appointment->load(['customer', 'branch', 'package', 'assignedStaff', 'queueEntry']);

        return Inertia::render('Appointments/Show', [
            'appointment' => $appointment,
        ]);
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,confirmed,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $appointment->update($validated);

        return back()->with('success', 'Appointment updated successfully.');
    }

    /**
     * Confirm a pending appointment.
     */
    public function confirm(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        if ($appointment->status !== 'pending') {
            return back()->withErrors(['status' => 'Only pending appointments can be confirmed.']);
        }

        $appointment->update(['status' => 'confirmed']);

        // TODO: Send confirmation notification to customer

        return back()->with('success', 'Appointment confirmed.');
    }

    /**
     * Start an appointment (convert to queue entry).
     */
    public function start(Request $request, Appointment $appointment)
    {
        $this->authorize('start', $appointment);

        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->withErrors(['status' => 'This appointment cannot be started.']);
        }

        $validated = $request->validate([
            'bay_id' => 'required|exists:bays,id',
        ]);

        // Create queue entry
        $queueEntry = QueueEntry::create([
            'branch_id' => $appointment->branch_id,
            'customer_id' => $appointment->customer_id,
            'package_id' => $appointment->package_id,
            'plate_number' => $appointment->plate_number,
            'vehicle_type' => $appointment->vehicle_type,
            'status' => 'in_progress',
            'position' => 0, // Appointments skip queue
        ]);

        // Create wash record
        $wash = Wash::create([
            'branch_id' => $appointment->branch_id,
            'customer_id' => $appointment->customer_id,
            'package_id' => $appointment->package_id,
            'bay_id' => $validated['bay_id'],
            'queue_entry_id' => $queueEntry->id,
            'started_at' => now(),
            'status' => 'in_progress',
            'total_amount' => $appointment->package->price,
        ]);

        // Update appointment
        $appointment->update([
            'status' => 'in_progress',
            'actual_start_at' => now(),
            'queue_entry_id' => $queueEntry->id,
            'assigned_to' => $request->user()->id,
        ]);

        // Update bay status
        \App\Models\Bay::find($validated['bay_id'])->update(['status' => 'active']);

        return back()->with('success', 'Appointment started successfully.');
    }

    /**
     * Complete an appointment.
     */
    public function complete(Request $request, Appointment $appointment)
    {
        $this->authorize('complete', $appointment);

        if ($appointment->status !== 'in_progress') {
            return back()->withErrors(['status' => 'Only in-progress appointments can be completed.']);
        }

        // Update appointment
        $appointment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update associated wash
        if ($appointment->queueEntry && $appointment->queueEntry->wash) {
            $wash = $appointment->queueEntry->wash;
            $wash->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Update bay status
            if ($wash->bay) {
                $wash->bay->update(['status' => 'idle']);
            }
        }

        // Update queue entry
        if ($appointment->queueEntry) {
            $appointment->queueEntry->update(['status' => 'completed']);
        }

        // Loyalty points are automatically awarded via WashObserver

        return back()->with('success', 'Appointment completed successfully.');
    }

    /**
     * Mark appointment as no-show.
     */
    public function markNoShow(Request $request, Appointment $appointment)
    {
        $this->authorize('markNoShow', $appointment);

        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->withErrors(['status' => 'Only pending or confirmed appointments can be marked as no-show.']);
        }

        $appointment->update(['status' => 'no_show']);

        return back()->with('success', 'Appointment marked as no-show.');
    }

    /**
     * Cancel an appointment.
     */
    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);

        if (!$appointment->canBeCancelled()) {
            return back()->withErrors(['appointment' => 'This appointment cannot be cancelled.']);
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }
}
