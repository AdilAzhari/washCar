<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;

final class InventoryPolicy
{
    public function viewAny(User $user): bool
    {
        // Staff, Manager, and Admin can view inventory
        return ! $user->isCustomer();
    }

    public function view(User $user, InventoryItem $inventoryItem): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager and Staff can view inventory in their branch
        return ($user->isManager() || $user->isStaff()) && $user->branch_id === $inventoryItem->branch_id;
    }

    public function create(User $user): bool
    {
        // Admin and Manager can create inventory items
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isManager();
    }

    public function update(User $user, InventoryItem $inventoryItem): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can update inventory in their branch
        return $user->isManager() && $user->branch_id === $inventoryItem->branch_id;
    }

    public function delete(User $user, InventoryItem $inventoryItem): bool
    {
        // Only Admin and Manager can delete
        return $this->update($user, $inventoryItem);
    }
}
