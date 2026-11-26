<?php

namespace App\Policies;

use App\Models\CatalogItem;
use App\Models\FoodOrderItem;
use App\Models\PlaySession;
use App\Models\RentalReservation;
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
        return $user->is_admin
            && ! FoodOrderItem::where('catalog_item_id', $item->id)->exists()
            && ! PlaySession::where('catalog_item_id', $item->id)->exists()
            && ! RentalReservation::where('catalog_item_id', $item->id)->exists();
    }
}
