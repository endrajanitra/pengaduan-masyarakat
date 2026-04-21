<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->string('file_path')->comment('Path relatif dari storage/app/public');
            $table->string('file_name')->comment('Nama file asli yang diupload user');
            $table->string('file_type', 20)->comment('Contoh: image/jpeg, application/pdf');
            $table->unsignedInteger('file_size')->comment('Ukuran file dalam bytes');
            $table->timestamp('created_at')->useCurrent();

            $table->index('complaint_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_attachments');
    }
};