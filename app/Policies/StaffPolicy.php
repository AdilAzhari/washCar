<?php

namespace App\Policies;

use App\Models\User;

class StaffPolicy
{
    public function viewAny(User $user): bool
    {
        // Admin and Manager can view staff
        return $user->isAdmin() || $user->isManager();
    }

    public function view(User $user, User $staff): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can view staff in their branch
        if ($user->isManager() && $user->branch_id === $staff->branch_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Admin and Manager can create staff
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, User $staff): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can update staff in their branch
        if ($user->isManager() && $user->branch_id === $staff->branch_id) {
            // Manager cannot modify other managers or admins
            if ($staff->isAdmin() || ($staff->isManager() && $staff->id !== $user->id)) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function delete(User $user, User $staff): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can delete staff (not managers or admins) in their branch
        if ($user->isManager() && $user->branch_id === $staff->branch_id) {
            // Manager cannot delete other managers or admins
            if ($staff->isAdmin() || $staff->isManager()) {
                return false;
            }

            return true;
        }

        return false;
    }
}
