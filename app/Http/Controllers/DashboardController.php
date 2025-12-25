<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Bay;
use App\Models\Package;
use App\Models\QueueEntry;
use App\Models\Wash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function index(): Response
    {
        // Get statistics
        $ongoingWashes = Wash::where('status', 'active')->count();
        $inQueue = QueueEntry::where('status', 'waiting')->count();
        $completedToday = Wash::where('status', 'completed')
            ->whereDate('completed_at', Carbon::today())
            ->count();

        // Calculate today's revenue
        $todayRevenue = Wash::where('status', 'completed')
            ->whereDate('completed_at', Carbon::today())
            ->join('packages', 'washes.package_id', '=', 'packages.id')
            ->sum('packages.price');

        // Get bays with their current washes and queue
        $bays = Bay::with([
            'branch',
            'washes' => function ($query): void {
                $query->where('status', 'active')
                    ->with(['customer', 'package'])
                    ->latest();
            },
        ])->get()->map(function ($bay): array {
            $currentWash = $bay->washes->first();
            $queueCount = QueueEntry::where('status', 'waiting')->count();

            return [
                'id' => $bay->id,
                'bayName' => $bay->name,
                'isOperational' => $bay->status !== 'maintenance',
                'status' => $bay->status,
                'currentWash' => $currentWash ? [
                    'id' => $currentWash->id,
                    'customerName' => $currentWash->customer->name ?? 'N/A',
                    'vehiclePlate' => $currentWash->customer->plate_number ?? 'N/A',
                    'packageName' => $currentWash->package->name ?? 'N/A',
                    'packageColor' => $currentWash->package->color ?? 'hsl(199 89% 48%)',
                    'progress' => $currentWash->progress,
                    'startedAt' => $currentWash->started_at,
                    'estimatedCompletion' => $currentWash->package
                        ? $currentWash->started_at->addMinutes($currentWash->package->duration_minutes)
                        : null,
                ] : null,
                'queueCount' => $queueCount,
            ];
        });

        // Get recent transactions (completed washes)
        $recentTransactions = Wash::where('status', 'completed')
            ->with(['customer', 'package'])
            ->latest('completed_at')
            ->limit(5)
            ->get()
            ->map(fn ($wash): array => [
                'id' => 'TXN-'.mb_str_pad((string) $wash->id, 3, '0', STR_PAD_LEFT),
                'customer' => $wash->customer->name ?? 'N/A',
                'plate' => $wash->customer->plate_number ?? 'N/A',
                'amount' => '$'.number_format((float) ($wash->package->price ?? 0), 0),
                'method' => 'Card', // You can add payment method field later
                'status' => 'completed',
                'completedAt' => $wash->completed_at,
            ]);

        // Get package sales for today
        $packageSales = Package::leftJoin('washes', function ($join): void {
            $join->on('packages.id', '=', 'washes.package_id')
                ->where('washes.status', 'completed')
                ->whereDate('washes.completed_at', Carbon::today());
        })
            ->select('packages.id', 'packages.name', 'packages.color', DB::raw('COUNT(washes.id) as count'))
            ->groupBy('packages.id', 'packages.name', 'packages.color')
            ->get();

        $stats = [
            'ongoingWashes' => $ongoingWashes,
            'inQueue' => $inQueue,
            'completedToday' => $completedToday,
            'todayRevenue' => '$'.number_format((float) $todayRevenue, 0),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'bays' => $bays,
            'recentTransactions' => $recentTransactions,
            'packageSales' => $packageSales,
        ]);
    }
}
