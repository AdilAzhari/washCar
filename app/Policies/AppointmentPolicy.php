<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view appointments (filtered by role in controller)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Customer can view their own appointments
        if ($user->isCustomer() && $appointment->customer_id === $user->id) {
            return true;
        }

        // Staff and Manager can view appointments for their branch
        if (($user->isStaff() || $user->isManager()) && $user->branch_id === $appointment->branch_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Customers can create their own appointments
        // Staff, Managers, and Admins can create appointments for customers
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        // Admin can update all
        if ($user->isAdmin()) {
            return true;
        }

        // Staff and Manager can update appointments for their branch
        if (($user->isStaff() || $user->isManager()) && $user->branch_id === $appointment->branch_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete/cancel the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        // Admin can delete all
        if ($user->isAdmin()) {
            return true;
        }

        // Customer can cancel their own appointments (if cancellable)
        if ($user->isCustomer() && $appointment->customer_id === $user->id && $appointment->canBeCancelled()) {
            return true;
        }

        // Staff and Manager can cancel appointments for their branch
        if (($user->isStaff() || $user->isManager()) && $user->branch_id === $appointment->branch_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Appointment $appointment): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can start an appointment.
     */
    public function start(User $user, Appointment $appointment): bool
    {
        // Only staff, manager, or admin can start appointments
        if ($user->isAdmin()) {
            return true;
        }

        if (($user->isStaff() || $user->isManager()) && $user->branch_id === $appointment->branch_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can mark an appointment as complete.
     */
    public function complete(User $user, Appointment $appointment): bool
    {
        return $this->start($user, $appointment);
    }

    /**
     * Determine whether the user can mark an appointment as no-show.
     */
    public function markNoShow(User $user, Appointment $appointment): bool
    {
        return $this->start($user, $appointment);
    }
}
