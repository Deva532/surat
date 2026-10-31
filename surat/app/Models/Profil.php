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
        'kepala_sekolah',
        'nip_kepala_sekolah',
        'logo',
    ];
}
