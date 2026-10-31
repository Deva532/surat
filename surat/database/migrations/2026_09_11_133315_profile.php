<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profile', function (Blueprint $table) {
            $table->id();
            $table->string('instansi');
            $table->string('dinas');
            $table->string('nama_sekolah');
            $table->string('kode_lembaga')->nullable();
            $table->string('alamat');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('kepala_sekolah');
            $table->string('nip_kepala_sekolah')->nullable();
            $table->enum('status', ['Definitif', 'Plt'])->default('Definitif');
            $table->string('logo')->nullable();
            $table->string('logo_pemda')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile');
    }
};
