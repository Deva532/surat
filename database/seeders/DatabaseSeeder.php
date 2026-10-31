<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Pastikan akun resmi Superadmin dan 7 Jabatan tersedia
        $this->call(JabatanUserSeeder::class);

        // 2. Isi seluruh data dummy persuratan (profil, kategori, surat masuk, disposisi, surat keluar)
        $this->call(DummyPersuratanSeeder::class);
    }
}
