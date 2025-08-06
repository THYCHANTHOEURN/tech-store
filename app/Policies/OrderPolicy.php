<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ANY_ORDER->value);
    }

    public function view(User $user, Order $order)
    {
        return $user->can(PermissionsEnum::VIEW_ORDER->value);
    }

    public function create(User $user)
    {
        return $user->can(PermissionsEnum::CREATE_ORDER->value);
    }

    public function update(User $user, Order $order)
    {
        return $user->can(PermissionsEnum::UPDATE_ORDER->value);
    }

    public function delete(User $user, Order $order)
    {
        return $user->can(PermissionsEnum::DELETE_ORDER->value);
    }

    public function restore(User $user, Order $order)
    {
        return $user->can(PermissionsEnum::RESTORE_ORDER->value);
    }

    public function forceDelete(User $user, Order $order)
    {
        return $user->can(PermissionsEnum::FORCE_DELETE_ORDER->value);
    }

    public function export(User $user)
    {
        return $user->can(PermissionsEnum::EXPORT_ORDER->value);
    }

    public function viewInvoice(User $user, Order $order)
    {
        return $user->can(PermissionsEnum::VIEW_ORDER_INVOICE->value);
    }
}
