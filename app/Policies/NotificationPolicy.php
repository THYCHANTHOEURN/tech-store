<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ANY_NOTIFICATION->value);
    }

    public function view(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_NOTIFICATION->value);
    }

    public function markAsRead(User $user)
    {
        return $user->can(PermissionsEnum::MARK_AS_READ_NOTIFICATION->value);
    }

    public function markAllAsRead(User $user)
    {
        return $user->can(PermissionsEnum::MARK_ALL_AS_READ_NOTIFICATION->value);
    }
}
