<?php

namespace App\Policies;

use App\Models\PlaySession;
use App\Models\User;

class PlaySessionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, PlaySession $record): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, PlaySession $record): bool
    {
        return false;
    }
}
