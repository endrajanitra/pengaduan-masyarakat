<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareSiteSettings
{
    /**
     * Bagikan data site_settings ke semua view Blade secara global.
     *
     * Dengan middleware ini, di setiap view Blade kamu bisa langsung pakai:
     *   {{ $siteName }}        → nama desa
     *   {{ $siteKepalaDesa }}  → nama kepala desa
     *   {{ $siteLogo }}        → URL logo desa
     *
     * Data diambil via SiteSetting::get() yang sudah pakai cache 24 jam,
     * jadi tidak ada query database ekstra per request.
     *
     * Daftarkan di bootstrap/app.php pada grup 'web':
     *   $middleware->appendToGroup('web', ShareSiteSettings::class);
     */
    public function handle(Request $request, Closure $next): Response
    {
        View::share([
            'siteName'        => SiteSetting::get('desa_name', 'Desa Wangisagara'),
            'siteKepalaDesa'  => SiteSetting::get('kepala_desa', '-'),
            'siteAlamat'      => SiteSetting::get('alamat_kantor', '-'),
            'sitePhone'       => SiteSetting::get('phone_kantor', '-'),
            'siteEmail'       => SiteSetting::get('email_kantor', '-'),
            'siteDescription' => SiteSetting::get('app_description', ''),
            'siteLogo'        => SiteSetting::get('logo_path')
                ? asset('storage/' . SiteSetting::get('logo_path'))
                : null,
        ]);

        return $next($request);
    }
}