<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Bay;
use App\Models\QueueEntry;
use App\Models\Wash;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class StaffDashboardController extends Controller
{
    /**
     * Display staff dashboard with queue and bay status.
     */
    public function index(Request $request): Response
    {
        $staff = $request->user();
        $branchId = $staff->branch_id;

        // Quick stats
        $waitingQueue = QueueEntry::where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->count();

        $activeBays = Bay::where('branch_id', $branchId)
            ->where('status', 'active')
            ->count();

        $idleBays = Bay::where('branch_id', $branchId)
            ->where('status', 'idle')
            ->count();

        $completedToday = Wash::where('branch_id', $branchId)
            ->whereDate('completed_at', today())
            ->where('status', 'completed')
            ->count();

        // Today's queue (waiting customers)
        $queueEntries = QueueEntry::where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->with(['customer', 'package'])
            ->orderBy('position')
            ->get();

        // Active washes (in progress)
        $activeWashes = Wash::where('branch_id', $branchId)
            ->where('status', 'in_progress')
            ->with(['customer', 'package', 'bay', 'queueEntry'])
            ->get();

        // Bay status grid
        $bays = Bay::where('branch_id', $branchId)
            ->with(['currentWash.customer', 'currentWash.package'])
            ->orderBy('name')
            ->get();

        // Today's appointments
        $todayAppointments = Appointment::where('branch_id', $branchId)
            ->whereDate('scheduled_at', today())
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->with(['customer', 'package'])
            ->orderBy('scheduled_at')
            ->get();

        return Inertia::render('Staff/Dashboard', [
            'stats' => [
                'waitingQueue' => $waitingQueue,
                'activeBays' => $activeBays,
                'idleBays' => $idleBays,
                'completedToday' => $completedToday,
            ],
            'queueEntries' => $queueEntries,
            'activeWashes' => $activeWashes,
            'bays' => $bays,
            'todayAppointments' => $todayAppointments,
        ]);
    }
}
