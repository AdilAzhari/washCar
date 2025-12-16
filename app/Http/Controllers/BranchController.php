<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\Wash;
use App\Models\QueueEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index(): Response
    {
        $branches = Branch::withCount(['bays', 'washes'])
            ->latest()
            ->get();

        return Inertia::render('Branches/Index', [
            'branches' => $branches,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Branches/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:branches',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'operating_hours' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Branch::create($validated);

        return redirect()->route('branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function show(Branch $branch): Response
    {
        $branch->load(['bays', 'users']);

        // Today's stats
        $today = now()->startOfDay();
        $todayWashes = Wash::where('branch_id', $branch->id)
            ->whereDate('created_at', $today)
            ->get();

        $todayStats = [
            'revenue' => $todayWashes->where('status', 'completed')
                ->sum(function ($wash) {
                    return $wash->package?->price ?? 0;
                }),
            'completed' => $todayWashes->where('status', 'completed')->count(),
            'in_progress' => $todayWashes->where('status', 'active')->count(),
            'waiting' => QueueEntry::where('branch_id', $branch->id)
                ->where('status', 'waiting')
                ->whereDate('created_at', $today)
                ->count(),
        ];

        // 6-month revenue trend
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $monthWashes = Wash::where('branch_id', $branch->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status', 'completed')
                ->with('package')
                ->get();

            $revenueData[] = [
                'month' => $month->format('M'),
                'revenue' => round($monthWashes->sum(function ($wash) {
                    return $wash->package?->price ?? 0;
                })),
                'washes' => $monthWashes->count(),
            ];
        }

        return Inertia::render('Branches/Show', [
            'branch' => $branch,
            'todayStats' => $todayStats,
            'revenueData' => $revenueData,
            'staff' => $branch->users,
        ]);
    }

    public function edit(Branch $branch): Response
    {
        return Inertia::render('Branches/Edit', [
            'branch' => $branch,
        ]);
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:branches,code,' . $branch->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'operating_hours' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', 'Branch deleted successfully.');
    }

    public function qrcode(Branch $branch): Response
    {
        return Inertia::render('Branches/QRCode', [
            'branch' => $branch,
            'appUrl' => config('app.url'),
        ]);
    }
}
