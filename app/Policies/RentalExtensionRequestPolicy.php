<?php

namespace App\Policies;

use App\Models\RentalExtensionRequest;
use App\Models\User;

class RentalExtensionRequestPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->is_admin;
    }

    public function update(User $u, RentalExtensionRequest $r): bool
    {
        return $u->is_admin;
    }

    public function delete(User $u, RentalExtensionRequest $r): bool
    {
        return false;
    }
}
