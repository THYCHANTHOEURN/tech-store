<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ANY_PRODUCT->value);
    }

    public function view(User $user, Product $product)
    {
        return $user->can(PermissionsEnum::VIEW_PRODUCT->value);
    }

    public function create(User $user)
    {
        return $user->can(PermissionsEnum::CREATE_PRODUCT->value);
    }

    public function update(User $user, Product $product)
    {
        return $user->can(PermissionsEnum::UPDATE_PRODUCT->value);
    }

    public function delete(User $user, Product $product)
    {
        return $user->can(PermissionsEnum::DELETE_PRODUCT->value);
    }

    public function restore(User $user, Product $product)
    {
        return $user->can(PermissionsEnum::RESTORE_PRODUCT->value);
    }

    public function forceDelete(User $user, Product $product)
    {
        return $user->can(PermissionsEnum::FORCE_DELETE_PRODUCT->value);
    }
}
