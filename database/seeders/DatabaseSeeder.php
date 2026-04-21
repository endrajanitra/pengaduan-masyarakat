<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------------------
        // 1. Super Admin
        // ----------------------------------------------------------------
        DB::table('users')->insert([
            'name'              => 'Super Admin',
            'email'             => 'admin@wangisagara.desa.id',
            'password'          => Hash::make('password'),
            'role'              => 'super_admin',
            'is_active'         => true,
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // ----------------------------------------------------------------
        // 2. Kepala Desa
        // ----------------------------------------------------------------
        DB::table('users')->insert([
            'name'              => 'Kepala Desa Wangisagara',
            'email'             => 'kepala@wangisagara.desa.id',
            'password'          => Hash::make('password'),
            'role'              => 'kepala_desa',
            'is_active'         => true,
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // ----------------------------------------------------------------
        // 3. Admin Desa
        // ----------------------------------------------------------------
        DB::table('users')->insert([
            'name'              => 'Admin Desa Wangisagara',
            'email'             => 'petugas@wangisagara.desa.id',
            'password'          => Hash::make('password'),
            'role'              => 'admin_desa',
            'is_active'         => true,
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // ----------------------------------------------------------------
        // 4. Pengaturan Situs (site_settings)
        // ----------------------------------------------------------------
        $settings = [
            ['key' => 'desa_name',       'value' => 'Desa Wangisagara',                        'type' => 'text',     'label' => 'Nama Desa'],
            ['key' => 'desa_kecamatan',  'value' => 'Majalaya',                                 'type' => 'text',     'label' => 'Kecamatan'],
            ['key' => 'desa_kabupaten',  'value' => 'Bandung',                                  'type' => 'text',     'label' => 'Kabupaten'],
            ['key' => 'desa_provinsi',   'value' => 'Jawa Barat',                               'type' => 'text',     'label' => 'Provinsi'],
            ['key' => 'alamat_kantor',   'value' => 'Jl. Raya Wangisagara No. 1, Majalaya',    'type' => 'textarea', 'label' => 'Alamat Kantor Desa'],
            ['key' => 'phone_kantor',    'value' => '022-xxxxxxx',                              'type' => 'text',     'label' => 'Nomor Telepon Kantor'],
            ['key' => 'email_kantor',    'value' => 'info@wangisagara.desa.id',                 'type' => 'text',     'label' => 'Email Kantor'],
            ['key' => 'kepala_desa',     'value' => 'Nama Kepala Desa',                         'type' => 'text',     'label' => 'Nama Kepala Desa'],
            ['key' => 'logo_path',       'value' => null,                                        'type' => 'image',    'label' => 'Logo Desa'],
            ['key' => 'app_description', 'value' => 'Sistem Pengaduan Masyarakat Desa Wangisagara — sampaikan aspirasi dan keluhan Anda secara mudah, transparan, dan terukur.', 'type' => 'textarea', 'label' => 'Deskripsi Aplikasi'],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->insert(array_merge($setting, ['updated_at' => now()]));
        }

        // ----------------------------------------------------------------
        // 5. Kategori Pengaduan
        // ----------------------------------------------------------------
        $categories = [
            ['name' => 'Infrastruktur Jalan',     'slug' => 'infrastruktur-jalan',     'icon' => 'heroicon-o-map',            'description' => 'Kerusakan jalan, jembatan, atau saluran air.',                      'sort_order' => 1],
            ['name' => 'Kebersihan & Sampah',      'slug' => 'kebersihan-sampah',        'icon' => 'heroicon-o-trash',           'description' => 'Masalah pengelolaan sampah dan kebersihan lingkungan.',             'sort_order' => 2],
            ['name' => 'Keamanan & Ketertiban',    'slug' => 'keamanan-ketertiban',      'icon' => 'heroicon-o-shield-check',    'description' => 'Gangguan keamanan, perkelahian, atau ketertiban umum.',             'sort_order' => 3],
            ['name' => 'Layanan Administrasi',     'slug' => 'layanan-administrasi',     'icon' => 'heroicon-o-document-text',   'description' => 'Kendala pengurusan surat, KTP, KK, atau dokumen kependudukan.',    'sort_order' => 4],
            ['name' => 'Penerangan Jalan',         'slug' => 'penerangan-jalan',         'icon' => 'heroicon-o-light-bulb',      'description' => 'Lampu jalan mati atau tidak berfungsi.',                           'sort_order' => 5],
            ['name' => 'Air Bersih & Sanitasi',    'slug' => 'air-bersih-sanitasi',      'icon' => 'heroicon-o-beaker',          'description' => 'Masalah air bersih, drainase, atau sanitasi lingkungan.',          'sort_order' => 6],
            ['name' => 'Sosial & Kemasyarakatan',  'slug' => 'sosial-kemasyarakatan',    'icon' => 'heroicon-o-user-group',      'description' => 'Konflik sosial, bantuan sosial, atau masalah kemasyarakatan.',     'sort_order' => 7],
            ['name' => 'Lainnya',                  'slug' => 'lainnya',                  'icon' => 'heroicon-o-ellipsis-horizontal', 'description' => 'Pengaduan lain yang tidak termasuk kategori di atas.',         'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert(array_merge($category, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}