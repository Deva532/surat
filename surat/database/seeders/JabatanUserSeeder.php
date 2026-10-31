<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JabatanUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Update atau buat user Superadmin / Admin
        $admin = User::firstOrNew(['email' => 'admin@lamongan.go.id']);
        if (!$admin->exists) {
            $admin->name = 'Administrator Kecamatan';
            $admin->nip = 198001012005011001;
            $admin->password = Hash::make('admin123');
            $admin->role = 'superadmin';
            $admin->jabatan = 'Administrator Sistem';
            $admin->save();
        }

        // Jika user izi (user lama id 2) ada, pastikan jabatannya terisi
        $izi = User::where('email', 'izi@gmail.com')->first();
        if ($izi) {
            $izi->jabatan = $izi->jabatan ?: 'Super Administrator';
            $izi->save();
        }

        // 2. Daftar 7 Jabatan Resmi Kecamatan Lamongan sesuai lembar disposisi
        $jabatanList = [
            [
                'name' => 'Sekretaris Kecamatan',
                'jabatan' => 'Sekretaris Kecamatan',
                'nip' => 197805122003121002,
                'email' => 'sekcam@lamongan.go.id',
                'password' => 'password123',
            ],
            [
                'name' => 'Kasi Pemerintahan',
                'jabatan' => 'Kepala Seksi Pemerintahan',
                'nip' => 198103152006041003,
                'email' => 'kasi.pemerintahan@lamongan.go.id',
                'password' => 'password123',
            ],
            [
                'name' => 'Kasi Pelayanan Publik',
                'jabatan' => 'Kepala Seksi Pelayanan Publik',
                'nip' => 198207202008012004,
                'email' => 'kasi.pelayanan@lamongan.go.id',
                'password' => 'password123',
            ],
            [
                'name' => 'Kasi Trantibum',
                'jabatan' => 'Kepala Seksi Ketentraman dan Ketertiban Umum',
                'nip' => 198309102009021005,
                'email' => 'kasi.trantibum@lamongan.go.id',
                'password' => 'password123',
            ],
            [
                'name' => 'Kasi PPM',
                'jabatan' => 'Kepala Seksi Pembangunan dan Pemberdayaan Masyarakat',
                'nip' => 198411052010011006,
                'email' => 'kasi.ppm@lamongan.go.id',
                'password' => 'password123',
            ],
            [
                'name' => 'Kasubag Umum & Kepegawaian',
                'jabatan' => 'Kepala Sub Bagian Umum dan Kepegawaian',
                'nip' => 198602182011012007,
                'email' => 'kasubag.umum@lamongan.go.id',
                'password' => 'password123',
            ],
            [
                'name' => 'Kasubag PEK',
                'jabatan' => 'Kepala Sub Bagian Perencanaan Evaluasi dan Keuangan',
                'nip' => 198708252012032008,
                'email' => 'kasubag.pek@lamongan.go.id',
                'password' => 'password123',
            ],
        ];

        foreach ($jabatanList as $j) {
            $user = User::firstOrNew(['email' => $j['email']]);
            $user->name = $j['name'];
            $user->jabatan = $j['jabatan'];
            $user->nip = $j['nip'];
            $user->role = 'jabatan';
            if (!$user->exists) {
                $user->password = Hash::make($j['password']);
            }
            $user->save();
        }
    }
}
