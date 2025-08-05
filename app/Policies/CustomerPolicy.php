<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ANY_CUSTOMER->value);
    }

    public function view(User $user, User $customer)
    {
        return $user->can(PermissionsEnum::VIEW_CUSTOMER->value);
    }

    public function create(User $user)
    {
        return $user->can(PermissionsEnum::CREATE_CUSTOMER->value);
    }

    public function update(User $user, User $customer)
    {
        return $user->can(PermissionsEnum::UPDATE_CUSTOMER->value);
    }

    public function delete(User $user, User $customer)
    {
        return $user->can(PermissionsEnum::DELETE_CUSTOMER->value);
    }

    public function restore(User $user, User $customer)
    {
        return $user->can(PermissionsEnum::RESTORE_CUSTOMER->value);
    }

    public function forceDelete(User $user, User $customer)
    {
        return $user->can(PermissionsEnum::FORCE_DELETE_CUSTOMER->value);
    }
}
