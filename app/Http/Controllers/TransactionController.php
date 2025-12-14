<?php

namespace App\Http\Controllers;

use App\Models\Wash;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(): Response
    {
        $transactions = Wash::with(['branch', 'customer', 'package', 'bay'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($wash) {
                return [
                    'id' => $wash->id,
                    'branch' => $wash->branch?->name,
                    'customer' => $wash->customer?->name,
                    'package' => $wash->package?->name,
                    'bay' => $wash->bay?->name,
                    'amount' => $wash->package?->price ?? 0,
                    'status' => $wash->status,
                    'started_at' => $wash->started_at,
                    'completed_at' => $wash->completed_at,
                    'created_at' => $wash->created_at,
                ];
            });

        $stats = [
            'total' => $transactions->count(),
            'completed' => $transactions->where('status', 'completed')->count(),
            'active' => $transactions->where('status', 'active')->count(),
            'revenue' => $transactions->where('status', 'completed')->sum('amount'),
        ];

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'stats' => $stats,
        ]);
    }
}
