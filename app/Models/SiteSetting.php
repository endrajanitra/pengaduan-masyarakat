<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    /**
     * Tabel ini hanya punya updated_at, tidak ada created_at.
     */
    public $timestamps    = false;
    const UPDATED_AT      = 'updated_at';

    protected $fillable = [
        'key',
        'value',
        'type',
        'label',
    ];

    // ----------------------------------------------------------------
    // Cache Key
    // ----------------------------------------------------------------

    const CACHE_KEY = 'site_settings_all';
    const CACHE_TTL = 60 * 60 * 24; // 24 jam

    // ----------------------------------------------------------------
    // Static Helper — Ambil & Simpan Setting
    // ----------------------------------------------------------------

    /**
     * Ambil semua setting sebagai array key => value, dengan cache.
     *
     * Contoh penggunaan di Blade:
     *   {{ SiteSetting::get('desa_name') }}
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Simpan atau perbarui nilai setting, lalu hapus cache.
     *
     * Contoh penggunaan:
     *   SiteSetting::set('desa_name', 'Desa Wangisagara');
     */
    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'updated_at' => now()]
        );

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Simpan banyak setting sekaligus dari array key => value.
     *
     * Contoh penggunaan:
     *   SiteSetting::setMany(['desa_name' => 'Wangisagara', 'kepala_desa' => 'Bapak Ujang']);
     */
    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            self::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Ambil semua setting sebagai koleksi penuh (untuk halaman pengaturan admin).
     */
    public static function all($columns = ['*'])
    {
        return parent::all($columns);
    }
}