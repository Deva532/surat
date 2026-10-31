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
        Schema::create('surat_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_agenda')->index();
            $table->string('nomor_surat');
            $table->string('pengirim');
            $table->string('penerima_dituju')->nullable();
            $table->text('perihal');
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima');
            $table->foreignId('kategori_id')->constrained('kategori_surat')->onDelete('cascade');
            $table->string('file_path');
            $table->enum('sifat', ['Biasa', 'Penting', 'Rahasia', 'Segera'])->default('Biasa');
            $table->enum('status', ['Diarsipkan', 'Proses Disposisi', 'Selesai'])->default('Diarsipkan');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuks');
    }
};
