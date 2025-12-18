<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QueueEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiQueueController extends Controller
{
    /**
     * Get customer's current queue position and status.
     *
     * This endpoint is used for real-time polling from the frontend.
     */
    public function myPosition(Request $request): JsonResponse
    {
        $customer = $request->user();

        // Find active queue entry for this customer
        $activeQueue = QueueEntry::where('customer_id', $customer->id)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->with(['branch', 'package'])
            ->first();

        // Customer not in queue
        if (!$activeQueue) {
            return response()->json([
                'in_queue' => false,
                'message' => 'You are not currently in any queue.',
            ]);
        }

        // Calculate current position (count customers ahead)
        $position = QueueEntry::where('branch_id', $activeQueue->branch_id)
            ->where('status', 'waiting')
            ->where('position', '<', $activeQueue->position)
            ->count() + 1;

        // Estimate wait time (20 minutes per position)
        $estimatedWaitMinutes = $position * 20;

        return response()->json([
            'in_queue' => true,
            'queue_entry_id' => $activeQueue->id,
            'status' => $activeQueue->status,
            'position' => $position,
            'estimated_wait_minutes' => $estimatedWaitMinutes,
            'branch' => [
                'id' => $activeQueue->branch->id,
                'name' => $activeQueue->branch->name,
                'code' => $activeQueue->branch->code,
            ],
            'package' => [
                'name' => $activeQueue->package->name,
                'estimated_duration' => $activeQueue->package->estimated_duration,
            ],
            'plate_number' => $activeQueue->plate_number,
            'vehicle_type' => $activeQueue->vehicle_type,
            'joined_at' => $activeQueue->created_at->toISOString(),
        ]);
    }

    /**
     * Get queue status for a specific branch (public endpoint).
     *
     * This can be used to display current queue length before joining.
     */
    public function branchQueueStatus(string $branchCode): JsonResponse
    {
        $branch = \App\Models\Branch::where('code', $branchCode)->firstOrFail();

        $waitingCount = QueueEntry::where('branch_id', $branch->id)
            ->where('status', 'waiting')
            ->count();

        $activeBays = \App\Models\Bay::where('branch_id', $branch->id)
            ->where('status', 'active')
            ->count();

        $availableBays = \App\Models\Bay::where('branch_id', $branch->id)
            ->where('status', 'idle')
            ->count();

        return response()->json([
            'branch' => [
                'id' => $branch->id,
                'name' => $branch->name,
                'code' => $branch->code,
            ],
            'queue_length' => $waitingCount,
            'active_bays' => $activeBays,
            'available_bays' => $availableBays,
            'estimated_wait_minutes' => $waitingCount * 20,
        ]);
    }
}
