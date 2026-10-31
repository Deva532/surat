<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_masuk_penerima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuks')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('jabatan');
            $table->enum('status', ['Terkirim', 'Dibaca', 'Diproses', 'Selesai'])->default('Terkirim');
            $table->text('catatan')->nullable();
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamp('selesai_pada')->nullable();
            $table->timestamps();

            $table->index(['surat_masuk_id', 'user_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk_penerima');
    }
};
