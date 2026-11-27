<?php

namespace App\Http\Controllers;

use App\Models\RentalReservation;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;

class RentalIdentityController extends Controller
{
    public function show(RentalReservation $reservation)
    {
        Gate::authorize('view', $reservation);
        $identity = $reservation->identity;
        abort_unless($identity, 404);
        if ($identity->expires_at <= now() || in_array($reservation->status, ['completed', 'cancelled', 'rejected'])) {
            $identity->delete();
            abort(404);
        }

        return response(base64_decode(Crypt::decryptString($identity->encrypted_photo)), 200, [
            'Content-Type' => 'image/jpeg', 'Content-Disposition' => 'inline; filename="identitas-sewa.jpg"',
            'Cache-Control' => 'private, no-store, max-age=0', 'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff', 'Referrer-Policy' => 'no-referrer', 'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
