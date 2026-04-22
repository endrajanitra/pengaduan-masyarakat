<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Daftar semua pengguna dengan filter role.
     * Hanya super_admin yang bisa mengakses.
     */
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $query = User::query();

        if ($request->filled('role')) {
            $query->byRole($request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('nik', 'like', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Form tambah pengguna baru (staf desa).
     */
    public function create(): View
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        return view('admin.users.form');
    }

    /**
     * Simpan pengguna baru — dipakai untuk membuat akun staf desa.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => ['required', 'in:admin_desa,kepala_desa,super_admin'],
            'phone'    => ['nullable', 'string', 'max:15'],
        ]);

        User::create(array_merge($validated, [
            'is_active'         => true,
            'email_verified_at' => now(),
        ]));

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Form edit pengguna.
     */
    public function edit(User $user): View
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        return view('admin.users.form', compact('user'));
    }

    /**
     * Update data pengguna.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role'  => ['required', 'in:warga,admin_desa,kepala_desa,super_admin'],
            'phone' => ['nullable', 'string', 'max:15'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Toggle aktif/nonaktif akun pengguna.
     */
    public function toggleActive(User $user): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        // Jangan sampai super admin menonaktifkan dirinya sendiri
        abort_if($user->id === auth()->id(), 422, 'Tidak bisa menonaktifkan akun sendiri.');

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    /**
     * Reset password pengguna oleh super admin.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update(['password' => $validated['password']]);

        return back()->with('success', "Password {$user->name} berhasil direset.");
    }
}