<?php

namespace App\Policies;

use App\Models\CatalogItem;
use App\Models\User;

class CatalogItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, CatalogItem $item): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, CatalogItem $item): bool
    {
        return $user->is_admin;
    }
}