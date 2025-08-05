<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ANY_SETTING->value);
    }

    public function view(User $user, Setting $setting)
    {
        return $user->can(PermissionsEnum::VIEW_SETTING->value);
    }

    public function create(User $user)
    {
        return $user->can(PermissionsEnum::CREATE_SETTING->value);
    }

    public function update(User $user, Setting $setting)
    {
        return $user->can(PermissionsEnum::UPDATE_SETTING->value);
    }

    public function delete(User $user, Setting $setting)
    {
        return $user->can(PermissionsEnum::DELETE_SETTING->value);
    }

    public function restore(User $user, Setting $setting)
    {
        return $user->can(PermissionsEnum::RESTORE_SETTING->value);
    }

    public function forceDelete(User $user, Setting $setting)
    {
        return $user->can(PermissionsEnum::FORCE_DELETE_SETTING->value);
    }
}
