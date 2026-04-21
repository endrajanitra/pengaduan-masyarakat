<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('complaint_number', 20)->unique()->comment('Format: ADU-2024-0001');
            $table->string('title');
            $table->text('description');
            $table->enum('status', [
                'draft',
                'submitted',
                'in_review',
                'in_progress',
                'resolved',
                'rejected',
            ])->default('submitted');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->string('location')->nullable()->comment('Nama lokasi kejadian, misal: RT 03 RW 02');
            $table->text('admin_response')->nullable()->comment('Balasan resmi dari admin setelah pengaduan selesai');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable()->comment('Waktu admin pertama kali mengubah status ke in_review');
            $table->timestamp('resolved_at')->nullable()->comment('Waktu pengaduan ditandai resolved');
            $table->boolean('is_anonymous')->default(false)->comment('Sembunyikan identitas pelapor dari publik');
            $table->boolean('is_public')->default(true)->comment('Tampilkan di halaman publik');
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index('user_id');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};