<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\QueueEntry;
use App\Models\Customer;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PublicQueueController extends Controller
{
    public function join(string $branchCode): Response
    {
        $branch = Branch::where('code', $branchCode)->firstOrFail();
        $packages = Package::where('is_active', true)->get()->map(function ($package) {
            return [
                'id' => $package->id,
                'name' => $package->name,
                'description' => $package->description,
                'price' => $package->price,
                'duration' => $package->duration_minutes,
                'color' => $package->color,
            ];
        });

        return Inertia::render('Public/QueueJoin', [
            'branch' => $branch,
            'packages' => $packages,
        ]);
    }

    public function submitJoin(Request $request, string $branchCode): RedirectResponse
    {
        $branch = Branch::where('code', $branchCode)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'plate_number' => 'required|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'package_id' => 'nullable|exists:packages,id',
            'special_requests' => 'nullable|string',
        ]);

        // Create or find customer
        $customer = Customer::firstOrCreate(
            ['phone' => $validated['phone']],
            [
                'name' => $validated['name'],
                'status' => 'active',
            ]
        );

        // Get next position in queue
        $maxPosition = QueueEntry::where('branch_id', $branch->id)
            ->where('status', 'waiting')
            ->max('position') ?? 0;

        // Create queue entry
        $queueEntry = QueueEntry::create([
            'branch_id' => $branch->id,
            'customer_id' => $customer->id,
            'package_id' => $validated['package_id'],
            'plate_number' => $validated['plate_number'],
            'position' => $maxPosition + 1,
            'status' => 'waiting',
            'joined_at' => now(),
        ]);

        return redirect()->route('queue.status', [
            'branchCode' => $branchCode,
            'queueId' => $queueEntry->id
        ]);
    }

    public function status(string $branchCode, int $queueId): Response
    {
        $branch = Branch::where('code', $branchCode)->firstOrFail();
        $queueEntry = QueueEntry::with(['customer', 'package'])
            ->where('id', $queueId)
            ->where('branch_id', $branch->id)
            ->firstOrFail();

        $stats = [
            'nowServing' => QueueEntry::where('branch_id', $branch->id)
                ->where('status', 'in_progress')
                ->min('position') ?? 0,
            'totalWaiting' => QueueEntry::where('branch_id', $branch->id)
                ->where('status', 'waiting')
                ->count(),
        ];

        return Inertia::render('Public/QueueStatus', [
            'branch' => $branch,
            'queueEntry' => $queueEntry,
            'stats' => $stats,
        ]);
    }
}
