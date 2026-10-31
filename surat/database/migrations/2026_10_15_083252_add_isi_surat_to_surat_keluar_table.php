<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->longText('isi_surat')->nullable()->after('perihal');
            $table->text('alamat_tujuan')->nullable()->after('penerima');
        });
    }

    public function down(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropColumn(['isi_surat', 'alamat_tujuan']);
        });
    }
};