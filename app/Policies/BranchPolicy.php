<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BranchPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all, Manager and Staff can view their own branch
        return $user->isAdmin() || $user->isManager() || $user->isStaff();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Branch $branch): bool
    {
        // Admin can view all branches
        if ($user->isAdmin()) {
            return true;
        }

        // Manager and Staff can only view their assigned branch
        if ($user->isManager() || $user->isStaff()) {
            return $user->branch_id === $branch->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create branches
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Branch $branch): bool
    {
        // Admin can update any branch
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can update their own branch
        if ($user->isManager()) {
            return $user->branch_id === $branch->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Branch $branch): bool
    {
        // Only admins can delete branches
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Branch $branch): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Branch $branch): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage staff for this branch.
     */
    public function manageStaff(User $user, Branch $branch): bool
    {
        // Admin can manage staff at any branch
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can manage staff at their own branch
        if ($user->isManager()) {
            return $user->branch_id === $branch->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view analytics across all branches.
     */
    public function viewAllAnalytics(User $user): bool
    {
        // Admin and Manager can view all branches analytics (read-only for managers)
        return $user->isAdmin() || $user->isManager();
    }
}
