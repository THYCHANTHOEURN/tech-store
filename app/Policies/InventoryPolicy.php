<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;


class InventoryPolicy
{
    public function viewInventory(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_INVENTORY->value);
    }

    public function updateInventory(User $user)
    {
        return $user->can(PermissionsEnum::UPDATE_INVENTORY->value);
    }

    public function bulkUpdateInventory(User $user)
    {
        return $user->can(PermissionsEnum::BULK_UPDATE_INVENTORY->value);
    }

    public function alertsInventory(User $user)
    {
        return $user->can(PermissionsEnum::ALERTS_INVENTORY->value);
    }
}
