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
        Schema::create('disposisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuks')->onDelete('cascade');
            $table->foreignId('pengirim_id')->comment('Pimpinan')->constrained('users');
            $table->foreignId('penerima_id')->comment('Staf/Penerima')->constrained('users');
            $table->text('isi_disposisi');
            $table->enum('sifat_instruksi', ['Biasa', 'Penting', 'Sangat Segera'])->default('Biasa');
            $table->date('batas_waktu')->nullable();
            $table->enum('status', ['Belum Dibaca', 'Proses', 'Selesai'])->default('Belum Dibaca');
            $table->text('catatan_tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisi');
    }
};
