<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
            $table->string('old_status', 20)->nullable()->comment('Null jika ini adalah log pertama saat pengaduan dibuat');
            $table->string('new_status', 20);
            $table->text('notes')->nullable()->comment('Catatan opsional saat mengubah status');
            $table->timestamp('created_at')->useCurrent();

            $table->index('complaint_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_logs');
    }
};