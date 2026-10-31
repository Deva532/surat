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
        // 1. Tambah pangkat_golongan ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('pangkat_golongan')->nullable()->after('jabatan')
                ->comment('Contoh: Pembina (IV/a)');
        });

        // 2. Tambah kolom ke tabel profile
        Schema::table('profile', function (Blueprint $table) {
            $table->string('pangkat_pimpinan')->nullable()->after('nip_kepala_sekolah')
                ->comment('Pangkat/Golongan Camat, misal: Pembina Tk. I (IV/b)');
            $table->string('website')->nullable()->after('email');
        });

        // 3. Upgrade tabel disposisi: tambah kolom baru, jadikan penerima_id nullable
        Schema::table('disposisi', function (Blueprint $table) {
            // Jadikan penerima_id nullable (backward compatible)
            $table->unsignedBigInteger('penerima_id')->nullable()->change();

            // Tambahan field untuk fitur multi-penerima dan instruksi resmi
            $table->date('tanggal_disposisi')->nullable()->after('pengirim_id')
                ->comment('Tanggal pembuatan disposisi, default hari ini');
            $table->json('penerima_jabatan')->nullable()->after('penerima_id')
                ->comment('Array jabatan penerima yang dipilih');
            $table->json('instruksi')->nullable()->after('isi_disposisi')
                ->comment('Checklist instruksi resmi: Tanggapan dan Saran, Proses Lebih Lanjut, dst');
            $table->text('instruksi_lainnya')->nullable()->after('instruksi')
                ->comment('Detail instruksi lainnya jika "Lain-lain" dipilih');
        });

        // 4. Buat tabel disposisi_penerima (pivot individual per penerima jabatan)
        Schema::create('disposisi_penerima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposisi_id')->constrained('disposisi')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('jabatan')->comment('Nama jabatan penerima');
            $table->enum('status', ['Belum Dibaca', 'Proses', 'Selesai'])->default('Belum Dibaca');
            $table->text('catatan_tindak_lanjut')->nullable();
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamp('selesai_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisi_penerima');

        Schema::table('disposisi', function (Blueprint $table) {
            $table->dropColumn(['tanggal_disposisi', 'penerima_jabatan', 'instruksi', 'instruksi_lainnya']);
            $table->unsignedBigInteger('penerima_id')->nullable(false)->change();
        });

        Schema::table('profile', function (Blueprint $table) {
            $table->dropColumn(['pangkat_pimpinan', 'website']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pangkat_golongan');
        });
    }
};
