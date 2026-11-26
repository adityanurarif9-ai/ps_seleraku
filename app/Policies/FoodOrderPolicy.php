<?php

namespace App\Policies;

use App\Models\FoodOrder;
use App\Models\User;

class FoodOrderPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->is_admin;
    }

    public function view(User $u, FoodOrder $o): bool
    {
        return $u->is_admin;
    }

    public function create(User $u): bool
    {
        return false;
    }

    public function update(User $u, FoodOrder $o): bool
    {
        return false;
    }

    public function delete(User $u, FoodOrder $o): bool
    {
        return false;
    }
}
