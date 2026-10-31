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
        Schema::table('users', function (Blueprint $table) {
            $table->string('jabatan')->nullable()->after('role');
            // Ubah tipe kolom role menjadi string agar fleksibel mendukung role superadmin, admin, jabatan
            $table->string('role', 50)->default('jabatan')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('jabatan');
            $table->enum('role', ['superadmin', 'admin'])->default('admin')->change();
        });
    }
};
