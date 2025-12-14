<?php

namespace App\Http\Controllers;

use App\Models\Bay;
use App\Models\Branch;
use App\Models\BayActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class BayController extends Controller
{
    public function index(): Response
    {
        $bays = Bay::with(['branch', 'washes' => function ($query) {
            $query->where('status', 'active')->latest();
        }])
        ->latest()
        ->get()
        ->map(function ($bay) {
            return [
                'id' => $bay->id,
                'name' => $bay->name,
                'status' => $bay->status,
                'branch' => $bay->branch,
                'currentWash' => $bay->washes->first(),
                'created_at' => $bay->created_at,
                'updated_at' => $bay->updated_at,
            ];
        });

        $branches = Branch::where('is_active', true)->get();

        return Inertia::render('Bays/Index', [
            'bays' => $bays,
            'branches' => $branches,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'status' => 'required|in:idle,active,maintenance',
        ]);

        $bay = Bay::create($validated);

        // Log the bay creation
        BayActivityLog::create([
            'bay_id' => $bay->id,
            'previous_status' => 'none',
            'new_status' => $validated['status'],
            'changed_by' => Auth::id(),
            'notes' => 'Bay created',
        ]);

        return redirect()->route('bays.index')
            ->with('success', 'Bay created successfully.');
    }

    public function update(Request $request, Bay $bay): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'status' => 'required|in:idle,active,maintenance',
        ]);

        $previousStatus = $bay->status;
        $bay->update($validated);

        // Log status change
        if ($previousStatus !== $validated['status']) {
            BayActivityLog::create([
                'bay_id' => $bay->id,
                'previous_status' => $previousStatus,
                'new_status' => $validated['status'],
                'changed_by' => Auth::id(),
                'notes' => $request->input('notes'),
            ]);
        }

        return redirect()->route('bays.index')
            ->with('success', 'Bay updated successfully.');
    }

    public function destroy(Bay $bay): RedirectResponse
    {
        $bay->delete();

        return redirect()->route('bays.index')
            ->with('success', 'Bay deleted successfully.');
    }

    public function updateStatus(Request $request, Bay $bay): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:idle,active,maintenance',
            'notes' => 'nullable|string',
        ]);

        $previousStatus = $bay->status;
        $bay->update(['status' => $validated['status']]);

        // Log status change
        BayActivityLog::create([
            'bay_id' => $bay->id,
            'previous_status' => $previousStatus,
            'new_status' => $validated['status'],
            'changed_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Bay status updated successfully.');
    }

    public function activityLog(Bay $bay): Response
    {
        $logs = BayActivityLog::where('bay_id', $bay->id)
            ->with('changedBy')
            ->latest('changed_at')
            ->get();

        return Inertia::render('Bays/ActivityLog', [
            'bay' => $bay,
            'logs' => $logs,
        ]);
    }
}
