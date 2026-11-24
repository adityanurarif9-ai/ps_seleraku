<?php

namespace App\Policies;

use App\Models\RentalReservation;
use App\Models\User;

class RentalReservationPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->is_admin;
    }

    public function view(User $u, RentalReservation $r): bool
    {
        return $u->is_admin || $r->user_id === $u->id;
    }

    public function create(User $u): bool
    {
        return true;
    }

    public function update(User $u, RentalReservation $r): bool
    {
        return $u->is_admin;
    }

    public function delete(User $u, RentalReservation $r): bool
    {
        return false;
    }
}
