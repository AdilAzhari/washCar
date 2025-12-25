<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wash;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CustomerWashHistoryController extends Controller
{
    /**
     * Display customer's wash history.
     */
    public function index(Request $request): Response
    {
        $customer = $request->user();

        // Build query with filters
        $query = Wash::where('customer_id', $customer->id)
            ->with(['branch', 'package', 'bay', 'queueEntry']);

        // Filter by date range if provided
        if ($request->has('from_date')) {
            $query->whereDate('completed_at', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('completed_at', '<=', $request->input('to_date'));
        }

        // Filter by branch if provided
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        // Filter by package if provided
        if ($request->has('package_id')) {
            $query->where('package_id', $request->input('package_id'));
        }

        // Only show completed washes
        $query->where('status', 'completed');

        // Order by most recent first
        $washes = $query->orderBy('completed_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get available filters
        $branches = \App\Models\Branch::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $packages = \App\Models\Package::orderBy('name')
            ->get(['id', 'name']);

        // Calculate totals
        $totalSpent = Wash::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalWashes = Wash::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->count();

        return Inertia::render('Customer/History', [
            'washes' => $washes,
            'branches' => $branches,
            'packages' => $packages,
            'filters' => $request->only(['from_date', 'to_date', 'branch_id', 'package_id']),
            'stats' => [
                'totalSpent' => $totalSpent,
                'totalWashes' => $totalWashes,
            ],
        ]);
    }
}
