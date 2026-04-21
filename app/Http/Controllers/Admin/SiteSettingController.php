<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    /**
     * Halaman pengaturan situs — hanya super admin.
     */
    public function index(): View
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $settings = SiteSetting::all()->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Simpan perubahan pengaturan situs.
     */
    public function update(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $validated = $request->validate([
            'desa_name'       => ['required', 'string', 'max:100'],
            'desa_kecamatan'  => ['required', 'string', 'max:100'],
            'desa_kabupaten'  => ['required', 'string', 'max:100'],
            'desa_provinsi'   => ['required', 'string', 'max:100'],
            'alamat_kantor'   => ['required', 'string', 'max:500'],
            'phone_kantor'    => ['nullable', 'string', 'max:20'],
            'email_kantor'    => ['nullable', 'email', 'max:100'],
            'kepala_desa'     => ['required', 'string', 'max:100'],
            'app_description' => ['nullable', 'string', 'max:500'],
            'logo'            => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        // Handle upload logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            $oldLogo = SiteSetting::get('logo_path');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('logo')->store('settings', 'public');
            $validated['logo_path'] = $path;
        }

        // Buang key 'logo' dari array sebelum disimpan (yang disimpan adalah logo_path)
        unset($validated['logo']);

        SiteSetting::setMany($validated);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}