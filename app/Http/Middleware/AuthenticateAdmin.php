<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;

class AuthenticateAdmin extends Authenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        $user = Filament::auth()->user();

        // Keep panel access restricted, but offer the correct sign-in page.
        if ($user && ! $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
            return redirect()->route('filament.admin.auth.login');
        }

        return parent::handle($request, $next, ...$guards);
    }
}
