<?php

namespace App\Policies;

use App\Models\Bay;
use App\Models\User;

class BayPolicy
{
    public function viewAny(User $user): bool
    {
        // Staff, Manager, and Admin can view bays
        return !$user->isCustomer();
    }

    public function view(User $user, Bay $bay): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager and Staff can view bays in their branch
        if (($user->isManager() || $user->isStaff()) && $user->branch_id === $bay->branch_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Admin and Manager can create bays
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, Bay $bay): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager and Staff can update bays in their branch
        if (($user->isManager() || $user->isStaff()) && $user->branch_id === $bay->branch_id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Bay $bay): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Only Manager can delete bays in their branch
        if ($user->isManager() && $user->branch_id === $bay->branch_id) {
            return true;
        }

        return false;
    }

    public function updateStatus(User $user, Bay $bay): bool
    {
        // Staff, Manager, and Admin can update bay status
        return $this->update($user, $bay);
    }
}
