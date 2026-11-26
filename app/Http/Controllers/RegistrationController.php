<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TurnstileVerification;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function store(Request $request, TurnstileVerification $verification)
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email'))), 'name' => trim((string) $request->input('name'))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(8), 'max:255', 'confirmed'],
        ], ['email.unique' => __('Email ini sudah terdaftar. Silakan masuk dengan akunmu.'), 'password.confirmed' => 'Konfirmasi kata sandi belum sama.', 'password.min' => 'Kata sandi minimal 8 karakter.']);
        $verification->validate($request);
        try {
            $user = User::create(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password'], 'is_admin' => false]);
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['email' => __('Email ini sudah terdaftar. Silakan masuk dengan akunmu.')]);
        }
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home-page')->with('success', __('Akun pemain berhasil dibuat. Selamat datang di PS Seleraku.'));
    }
}
