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
        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id();
            // Komponen Penomoran Surat
            $table->string('kode_nomenklatur'); 
            $table->integer('nomor_urut');       
            $table->string('kode_instansi');    
            $table->year('tahun');              
            $table->string('nomor_surat')->unique(); // Hasil gabungan: 005/123/DISKOMINFO/2026
            
            // Detail Surat
            $table->string('penerima');
            $table->text('perihal');
            $table->date('tanggal_surat');
            $table->foreignId('kategori_id')->constrained('kategori_surat')->onDelete('cascade');
            $table->string('file_path');
            
            $table->enum('sifat', ['Biasa', 'Penting', 'Rahasia', 'Segera'])->default('Biasa');
            $table->enum('status', ['Draft', 'Menunggu Persetujuan', 'Disetujui', 'Terkirim'])->default('Draft');
            
            $table->foreignId('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluar');
    }
};
