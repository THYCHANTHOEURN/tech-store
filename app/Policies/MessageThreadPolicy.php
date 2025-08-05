<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;
use App\Models\MessageThread;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessageThreadPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ANY_MESSAGE_THREAD->value);
    }

    public function view(User $user, MessageThread $thread)
    {
        return $user->can(PermissionsEnum::VIEW_MESSAGE_THREAD->value);
    }

    public function reply(User $user, MessageThread $thread)
    {
        return $user->can(PermissionsEnum::REPLY_MESSAGE_THREAD->value);
    }

    public function close(User $user, MessageThread $thread)
    {
        return $user->can(PermissionsEnum::CLOSE_MESSAGE_THREAD->value);
    }

    public function reopen(User $user, MessageThread $thread)
    {
        return $user->can(PermissionsEnum::REOPEN_MESSAGE_THREAD->value);
    }
}
