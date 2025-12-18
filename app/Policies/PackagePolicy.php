<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;

class PackagePolicy
{
    public function viewAny(User $user): bool
    {
        // All authenticated users can view packages
        return true;
    }

    public function view(User $user, Package $package): bool
    {
        // All authenticated users can view packages
        return true;
    }

    public function create(User $user): bool
    {
        // Admin and Manager can create packages
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, Package $package): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can update packages for their branch (branch-specific packages)
        if ($user->isManager()) {
            // If package is branch-specific, check ownership
            if ($package->branch_id) {
                return $user->branch_id === $package->branch_id;
            }

            // If package is global (no branch_id), only admin can update
            return false;
        }

        return false;
    }

    public function delete(User $user, Package $package): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can delete packages for their branch
        if ($user->isManager() && $package->branch_id) {
            return $user->branch_id === $package->branch_id;
        }

        return false;
    }
}
