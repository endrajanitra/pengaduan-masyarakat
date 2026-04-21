<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ----------------------------------------------------------------
    // Register
    // ----------------------------------------------------------------

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'nik'      => ['required', 'digits:16', 'unique:users,nik'],
            'phone'    => ['required', 'string', 'max:15'],
            'address'  => ['required', 'string', 'max:500'],
            'rt_rw'    => ['required', 'string', 'max:10'],
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => $validated['password'],
            'nik'       => $validated['nik'],
            'phone'     => $validated['phone'],
            'address'   => $validated['address'],
            'rt_rw'     => $validated['rt_rw'],
            'role'      => 'warga',
            'is_active' => false, // aktif setelah verifikasi email
        ]);

        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')
            ->with('success', 'Akun berhasil dibuat. Silakan cek email untuk verifikasi.');
    }

    // ----------------------------------------------------------------
    // Login
    // ----------------------------------------------------------------

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun Anda belum aktif. Silakan verifikasi email terlebih dahulu.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectByRole($user->role));
    }

    // ----------------------------------------------------------------
    // Logout
    // ----------------------------------------------------------------

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ----------------------------------------------------------------
    // Private Helper
    // ----------------------------------------------------------------

    private function redirectByRole(string $role): string
    {
        return match ($role) {
            'super_admin', 'admin_desa', 'kepala_desa' => route('admin.dashboard'),
            default => route('warga.dashboard'),
        };
    }
}