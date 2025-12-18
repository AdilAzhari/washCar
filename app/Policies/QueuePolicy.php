<?php

namespace App\Policies;

use App\Models\QueueEntry;
use App\Models\User;

class QueuePolicy
{
    public function viewAny(User $user): bool
    {
        // Staff, Manager, and Admin can view queue
        return ! $user->isCustomer();
    }

    public function view(User $user, QueueEntry $queueEntry): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager and Staff can view queue entries in their branch
        if (($user->isManager() || $user->isStaff()) && $user->branch_id === $queueEntry->branch_id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Anyone can create queue entries (public queue join)
        return true;
    }

    public function update(User $user, QueueEntry $queueEntry): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager and Staff can update queue entries in their branch
        if (($user->isManager() || $user->isStaff()) && $user->branch_id === $queueEntry->branch_id) {
            return true;
        }

        return false;
    }

    public function delete(User $user, QueueEntry $queueEntry): bool
    {
        return $this->update($user, $queueEntry);
    }

    public function start(User $user, QueueEntry $queueEntry): bool
    {
        // Staff, Manager, and Admin can start washes
        if ($user->isAdmin()) {
            return true;
        }

        if (($user->isManager() || $user->isStaff()) && $user->branch_id === $queueEntry->branch_id) {
            return true;
        }

        return false;
    }

    public function complete(User $user, QueueEntry $queueEntry): bool
    {
        return $this->start($user, $queueEntry);
    }

    public function cancel(User $user, QueueEntry $queueEntry): bool
    {
        return $this->start($user, $queueEntry);
    }
}
