<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratKeluar extends Model
{
    use SoftDeletes;

    protected $table = 'surat_keluar';

    protected $fillable = [
        'kode_nomenklatur',
        'nomor_urut',
        'kode_instansi',
        'tahun',
        'nomor_surat',
        'penerima',
        'alamat_tujuan',
        'perihal',
        'isi_surat',
        'tanggal_surat',
        'kategori_id',
        'file_path',
        'sifat',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'tahun' => 'integer',
            'nomor_urut' => 'integer',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(
            KategoriSurat::class,
            'kategori_id'
        );
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
