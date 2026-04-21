<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Contoh: desa_name, logo_path, kepala_desa, alamat_kantor');
            $table->text('value')->nullable();
            $table->string('type', 20)->default('text')->comment('text, textarea, image, boolean');
            $table->string('label')->nullable()->comment('Label tampilan di halaman pengaturan');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};