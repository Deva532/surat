<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $table = 'profile';

    protected $fillable = [
        'instansi',
        'dinas',
        'nama_sekolah',
        'alamat',
        'kecamatan',
        'kabupaten',
        'telepon',
        'email',
        'website',
        'kepala_sekolah',
        'nip_kepala_sekolah',
        'pangkat_pimpinan',
        'status',
        'logo',
        'logo_pemda',
        'kode_lembaga',
    ];
}
