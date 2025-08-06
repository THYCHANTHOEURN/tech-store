<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;
use App\Models\Banner;
use Illuminate\Auth\Access\HandlesAuthorization;

class BannerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ANY_BANNER->value);
    }

    public function view(User $user, Banner $banner)
    {
        return $user->can(PermissionsEnum::VIEW_BANNER->value);
    }

    public function create(User $user)
    {
        return $user->can(PermissionsEnum::CREATE_BANNER->value);
    }

    public function update(User $user, Banner $banner)
    {
        return $user->can(PermissionsEnum::UPDATE_BANNER->value);
    }

    public function delete(User $user, Banner $banner)
    {
        return $user->can(PermissionsEnum::DELETE_BANNER->value);
    }

    public function restore(User $user, Banner $banner)
    {
        return $user->can(PermissionsEnum::RESTORE_BANNER->value);
    }

    public function forceDelete(User $user, Banner $banner)
    {
        return $user->can(PermissionsEnum::FORCE_DELETE_BANNER->value);
    }

    public function export(User $user)
    {
        return $user->can(PermissionsEnum::EXPORT_BANNER->value);
    }
}
