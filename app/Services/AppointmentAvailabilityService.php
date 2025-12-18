<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Wash;
use Carbon\Carbon;

class AppointmentAvailabilityService
{
    /**
     * Get available appointment slots for a branch on a specific date.
     *
     * @param Branch $branch
     * @param Carbon $date
     * @return array Array of available time slots with bay availability
     */
    public function getAvailableSlots(Branch $branch, Carbon $date): array
    {
        // Get branch operating hours
        $openingTime = Carbon::parse($branch->opening_time);
        $closingTime = Carbon::parse($branch->closing_time);

        // Get all bays for this branch
        $totalBays = $branch->bays()->where('status', '!=', 'maintenance')->count();

        if ($totalBays === 0) {
            return [];
        }

        // Generate time slots (every 30 minutes)
        $slots = [];
        $currentSlot = $date->copy()->setTimeFrom($openingTime);
        $endTime = $date->copy()->setTimeFrom($closingTime);

        while ($currentSlot < $endTime) {
            // Don't show slots in the past
            if ($currentSlot > now()) {
                $slots[] = $currentSlot->copy();
            }
            $currentSlot->addMinutes(30);
        }

        // Get existing appointments and active washes for this date
        $existingAppointments = Appointment::where('branch_id', $branch->id)
            ->whereDate('scheduled_at', $date)
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->get();

        $activeWashes = Wash::where('branch_id', $branch->id)
            ->whereDate('started_at', $date)
            ->whereIn('status', ['in_progress'])
            ->get();

        // Calculate availability for each slot
        $availableSlots = [];

        foreach ($slots as $slot) {
            // Count how many bays are occupied at this time slot
            $occupiedBays = 0;

            // Count appointments at this time (assuming 30 min per wash)
            foreach ($existingAppointments as $appointment) {
                $appointmentStart = Carbon::parse($appointment->scheduled_at);
                $appointmentEnd = $appointmentStart->copy()->addMinutes(30);

                if ($slot >= $appointmentStart && $slot < $appointmentEnd) {
                    $occupiedBays++;
                }
            }

            // Count active washes (assuming 30 min average duration)
            foreach ($activeWashes as $wash) {
                $washStart = Carbon::parse($wash->started_at);
                $washEnd = $washStart->copy()->addMinutes(30);

                if ($slot >= $washStart && $slot < $washEnd) {
                    $occupiedBays++;
                }
            }

            $availableBays = $totalBays - $occupiedBays;

            if ($availableBays > 0) {
                $availableSlots[] = [
                    'time' => $slot->format('H:i'),
                    'datetime' => $slot->toIso8601String(),
                    'availableBays' => $availableBays,
                    'totalBays' => $totalBays,
                    'isAvailable' => true,
                ];
            }
        }

        return $availableSlots;
    }

    /**
     * Check if a specific time slot is available.
     */
    public function isSlotAvailable(Branch $branch, Carbon $datetime): bool
    {
        $date = $datetime->copy()->startOfDay();
        $availableSlots = $this->getAvailableSlots($branch, $date);

        $requestedTime = $datetime->format('H:i');

        foreach ($availableSlots as $slot) {
            if ($slot['time'] === $requestedTime) {
                return true;
            }
        }

        return false;
    }
}
