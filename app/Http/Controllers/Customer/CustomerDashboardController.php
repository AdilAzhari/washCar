<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerDashboardController extends Controller
{
    /**
     * Display the customer dashboard.
     */
    public function index(Request $request): Response
    {
        $customer = $request->user();

        // Get or create loyalty points record
        $loyaltyPoints = $customer->loyaltyPoints()->firstOrCreate(
            ['customer_id' => $customer->id],
            [
                'points' => 0,
                'lifetime_points' => 0,
                'tier' => 'bronze',
            ]
        );

        // Get upcoming appointments (next 3)
        $upcomingAppointments = $customer->appointments()
            ->with(['branch', 'package'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->limit(3)
            ->get();

        // Get recent wash history (last 5)
        $recentWashes = \App\Models\Wash::where('customer_id', $customer->id)
            ->with(['branch', 'package', 'bay'])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        // Check if customer is currently in queue
        $activeQueue = \App\Models\QueueEntry::where('customer_id', $customer->id)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->with(['branch', 'package'])
            ->first();

        return Inertia::render('Customer/Dashboard', [
            'loyaltyPoints' => $loyaltyPoints,
            'upcomingAppointments' => $upcomingAppointments,
            'recentWashes' => $recentWashes,
            'activeQueue' => $activeQueue,
        ]);
    }
}
