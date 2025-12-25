<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class StaffPolicy
{
    public function viewAny(User $user): bool
    {
        // Admin and Manager can view staff
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isManager();
    }

    public function view(User $user, User $staff): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can view staff in their branch
        return $user->isManager() && $user->branch_id === $staff->branch_id;
    }

    public function create(User $user): bool
    {
        // Admin and Manager can create staff
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isManager();
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
            return ! $staff->isAdmin() && ! $staff->isManager();
        }

        return false;
    }
}
