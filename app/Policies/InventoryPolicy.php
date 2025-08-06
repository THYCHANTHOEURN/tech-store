<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;


class InventoryPolicy
{
    public function view(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_INVENTORY->value);
    }

    public function update(User $user)
    {
        return $user->can(PermissionsEnum::UPDATE_INVENTORY->value);
    }

    public function bulkUpdate(User $user)
    {
        return $user->can(PermissionsEnum::BULK_UPDATE_INVENTORY->value);
    }

    public function alerts(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_INVENTORY_ALERTS->value);
    }
}
