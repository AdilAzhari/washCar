<?php

namespace App\Http\Controllers;

use App\Models\Bay;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Package;
use App\Models\QueueEntry;
use App\Models\Wash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QueueController extends Controller
{
    public function index(): Response
    {
        $queueEntries = QueueEntry::with(['branch', 'customer', 'package'])
            ->orderBy('position')
            ->get();

        $branches = Branch::all();
        $customers = Customer::where('status', 'active')->get();
        $packages = Package::where('is_active', true)->get();

        return Inertia::render('Queue/Index', [
            'queueEntries' => $queueEntries,
            'branches' => $branches,
            'customers' => $customers,
            'packages' => $packages,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'nullable|exists:customers,id',
            'package_id' => 'nullable|exists:packages,id',
            'plate_number' => 'required|string|max:255',
        ]);

        $maxPosition = QueueEntry::where('branch_id', $validated['branch_id'])
            ->where('status', 'waiting')
            ->max('position') ?? 0;

        $validated['position'] = $maxPosition + 1;
        $validated['status'] = 'waiting';

        QueueEntry::create($validated);

        return back()->with('success', 'Queue entry created successfully.');
    }

    public function update(Request $request, QueueEntry $queue): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:waiting,in_progress,completed,cancelled',
        ]);

        if ($validated['status'] === 'in_progress' && ! $queue->started_at) {
            $validated['started_at'] = now();
        }

        if (in_array($validated['status'], ['completed', 'cancelled'])) {
            $validated['completed_at'] = now();
        }

        $queue->update($validated);

        return back()->with('success', 'Queue entry updated successfully.');
    }

    public function destroy(QueueEntry $queue): RedirectResponse
    {
        $queue->delete();

        return back()->with('success', 'Queue entry deleted successfully.');
    }

    public function viewQueue(): Response
    {
        $waitingQueue = QueueEntry::with(['branch', 'customer', 'package'])
            ->where('status', 'waiting')
            ->orderBy('position')
            ->get();

        $inProgressWashes = Wash::with(['branch', 'customer', 'package', 'bay'])
            ->where('status', 'active')
            ->get();

        $totalWaiting = $waitingQueue->count();
        $inProgress = $inProgressWashes->count();
        $availableBays = Bay::where('status', 'idle')->count();

        // Calculate average wait time
        $avgWaitTime = $waitingQueue->isEmpty() ? 0 :
            $waitingQueue->avg(function ($entry) {
                return $entry->joined_at ? now()->diffInMinutes($entry->joined_at) : 0;
            });

        return Inertia::render('Queue/ViewQueue', [
            'waitingQueue' => $waitingQueue,
            'inProgressWashes' => $inProgressWashes,
            'stats' => [
                'totalWaiting' => $totalWaiting,
                'inProgress' => $inProgress,
                'averageWaitTime' => round($avgWaitTime),
                'availableBays' => $availableBays,
            ],
        ]);
    }

    public function start(QueueEntry $queue): RedirectResponse
    {
        // Find an available bay in the same branch
        $bay = Bay::where('branch_id', $queue->branch_id)
            ->where('status', 'idle')
            ->first();

        if (! $bay) {
            return back()->with('error', 'No available bays in this branch.');
        }

        // Create wash record
        $wash = Wash::create([
            'queue_entry_id' => $queue->id,
            'branch_id' => $queue->branch_id,
            'customer_id' => $queue->customer_id,
            'package_id' => $queue->package_id,
            'bay_id' => $bay->id,
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Update queue entry
        $queue->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Update bay status
        $bay->update(['status' => 'active']);

        return back()->with('success', 'Wash started successfully.');
    }

    public function cancel(QueueEntry $queue): RedirectResponse
    {
        $queue->update([
            'status' => 'cancelled',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Queue entry cancelled.');
    }

    public function completeWash(Wash $wash): RedirectResponse
    {
        // Update wash
        $wash->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update related queue entry if exists
        if ($wash->queue_entry_id) {
            QueueEntry::where('id', $wash->queue_entry_id)
                ->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
        }

        // Free up the bay
        if ($wash->bay) {
            $wash->bay->update(['status' => 'idle']);
        }

        return back()->with('success', 'Wash completed successfully.');
    }

    public function cancelWash(Wash $wash): RedirectResponse
    {
        // Update wash
        $wash->update([
            'status' => 'cancelled',
            'completed_at' => now(),
        ]);

        // Update related queue entry if exists
        if ($wash->queue_entry_id) {
            QueueEntry::where('id', $wash->queue_entry_id)
                ->update([
                    'status' => 'cancelled',
                    'completed_at' => now(),
                ]);
        }

        // Free up the bay
        if ($wash->bay) {
            $wash->bay->update(['status' => 'idle']);
        }

        return back()->with('success', 'Wash cancelled.');
    }
}
