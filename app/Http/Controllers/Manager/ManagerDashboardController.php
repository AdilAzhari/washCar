<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Bay;
use App\Models\Branch;
use App\Models\InventoryItem;
use App\Models\QueueEntry;
use App\Models\Wash;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ManagerDashboardController extends Controller
{
    /**
     * Display manager dashboard with branch KPIs.
     */
    public function index(Request $request): Response
    {
        $manager = $request->user();
        $branchId = $manager->branch_id;

        // Branch KPIs - Today
        $todayRevenue = Wash::where('branch_id', $branchId)
            ->whereDate('completed_at', today())
            ->where('status', 'completed')
            ->sum('total_amount');

        $todayWashes = Wash::where('branch_id', $branchId)
            ->whereDate('completed_at', today())
            ->where('status', 'completed')
            ->count();

        // Week stats
        $weekRevenue = Wash::where('branch_id', $branchId)
            ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', 'completed')
            ->sum('total_amount');

        // Month stats
        $monthRevenue = Wash::where('branch_id', $branchId)
            ->whereMonth('completed_at', now()->month)
            ->where('status', 'completed')
            ->sum('total_amount');

        // Bay status
        $activeBays = Bay::where('branch_id', $branchId)->where('status', 'active')->count();
        $idleBays = Bay::where('branch_id', $branchId)->where('status', 'idle')->count();
        $maintenanceBays = Bay::where('branch_id', $branchId)->where('status', 'maintenance')->count();

        // Current queue
        $waitingQueue = QueueEntry::where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->count();

        // Upcoming appointments (today + tomorrow)
        $upcomingAppointments = Appointment::where('branch_id', $branchId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereBetween('scheduled_at', [now(), now()->addDays(2)])
            ->with(['customer', 'package'])
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();

        // Revenue chart - Last 7 days
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Wash::where('branch_id', $branchId)
                ->whereDate('completed_at', $date)
                ->where('status', 'completed')
                ->sum('total_amount');

            $revenueChart[] = [
                'date' => $date->format('M d'),
                'revenue' => (float) $revenue,
            ];
        }

        // Top packages this month
        $topPackages = Wash::where('branch_id', $branchId)
            ->whereMonth('completed_at', now()->month)
            ->where('status', 'completed')
            ->select('package_id', \DB::raw('count(*) as count'), \DB::raw('sum(total_amount) as revenue'))
            ->groupBy('package_id')
            ->with('package')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Staff performance this month
        $staffPerformance = Wash::where('branch_id', $branchId)
            ->whereMonth('completed_at', now()->month)
            ->where('status', 'completed')
            ->whereNotNull('bay_id')
            ->select(\DB::raw('count(*) as washes_completed'))
            ->addSelect(\DB::raw('sum(total_amount) as revenue_generated'))
            ->get();

        // Low stock alerts
        $lowStockItems = InventoryItem::where('branch_id', $branchId)
            ->whereRaw('quantity <= low_stock_threshold')
            ->orderBy('quantity')
            ->limit(5)
            ->get();

        return Inertia::render('Manager/Dashboard', [
            'kpis' => [
                'todayRevenue' => $todayRevenue,
                'todayWashes' => $todayWashes,
                'weekRevenue' => $weekRevenue,
                'monthRevenue' => $monthRevenue,
                'activeBays' => $activeBays,
                'idleBays' => $idleBays,
                'maintenanceBays' => $maintenanceBays,
                'waitingQueue' => $waitingQueue,
            ],
            'revenueChart' => $revenueChart,
            'topPackages' => $topPackages,
            'upcomingAppointments' => $upcomingAppointments,
            'lowStockItems' => $lowStockItems,
        ]);
    }

    /**
     * Display detailed reports for manager's branch.
     */
    public function reports(Request $request): Response
    {
        $manager = $request->user();
        $branchId = $manager->branch_id;

        // Get date range from request or default to this month
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        // Detailed revenue breakdown
        $totalRevenue = Wash::where('branch_id', $branchId)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalWashes = Wash::where('branch_id', $branchId)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        $averageTicket = $totalWashes > 0 ? $totalRevenue / $totalWashes : 0;

        // Package breakdown
        $packageBreakdown = Wash::where('branch_id', $branchId)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select('package_id', \DB::raw('count(*) as count'), \DB::raw('sum(total_amount) as revenue'))
            ->groupBy('package_id')
            ->with('package')
            ->get();

        return Inertia::render('Manager/Reports', [
            'dateRange' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'summary' => [
                'totalRevenue' => $totalRevenue,
                'totalWashes' => $totalWashes,
                'averageTicket' => $averageTicket,
            ],
            'packageBreakdown' => $packageBreakdown,
        ]);
    }

    /**
     * Display analytics across all branches (read-only for managers).
     */
    public function allBranchesAnalytics(Request $request): Response
    {
        $this->authorize('viewAllAnalytics', Branch::class);

        // Get all branches with their stats
        $branches = Branch::where('active', true)
            ->get()
            ->map(function ($branch) {
                $todayRevenue = Wash::where('branch_id', $branch->id)
                    ->whereDate('completed_at', today())
                    ->where('status', 'completed')
                    ->sum('total_amount');

                $monthRevenue = Wash::where('branch_id', $branch->id)
                    ->whereMonth('completed_at', now()->month)
                    ->where('status', 'completed')
                    ->sum('total_amount');

                $activeBays = Bay::where('branch_id', $branch->id)->where('status', 'active')->count();
                $totalBays = Bay::where('branch_id', $branch->id)->count();

                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'code' => $branch->code,
                    'todayRevenue' => $todayRevenue,
                    'monthRevenue' => $monthRevenue,
                    'activeBays' => $activeBays,
                    'totalBays' => $totalBays,
                ];
            });

        return Inertia::render('Manager/AllBranchesAnalytics', [
            'branches' => $branches,
        ]);
    }
}
