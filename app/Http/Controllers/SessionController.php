<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TurnstileVerification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function landing()
    {
        return Auth::check() ? redirect()->route('home-page') : view('login');
    }

    public function store(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (config('app.demo_accounts') && User::query()->count() === 0) {
            app(DatabaseSeeder::class)->run();
        }

        app(TurnstileVerification::class)->validate($request);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => __('Email atau kata sandi salah.')]);
        }

        $request->session()->regenerate();

        return redirect()->intended(auth()->user()->is_admin ? url('/admin') : route('home-page'));
    }

    public function destroy(Request $request)
    {
        $locale = $request->session()->get('locale', 'id');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->put('locale', $locale);

        return redirect()->route('login');
    }
}
