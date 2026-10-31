<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disposisi extends Model
{
    protected $table = 'disposisi';

    protected $fillable = [
        'surat_masuk_id',
        'pengirim_id',
        'penerima_id',
        'tanggal_disposisi',
        'penerima_jabatan',
        'isi_disposisi',
        'instruksi',
        'instruksi_lainnya',
        'sifat_instruksi',
        'batas_waktu',
        'status',
        'catatan_tindak_lanjut',
    ];

    protected function casts(): array
    {
        return [
            'batas_waktu'       => 'date',
            'tanggal_disposisi' => 'date',
            'penerima_jabatan'  => 'array',
            'instruksi'         => 'array',
        ];
    }

    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    public function penerimaList(): HasMany
    {
        return $this->hasMany(DisposisiPenerima::class, 'disposisi_id');
    }

    /**
     * Daftar semua jabatan yang dipilih sebagai penerima disposisi.
     */
    public function getPenerimaJabatanListAttribute(): array
    {
        return $this->penerima_jabatan ?? [];
    }

    /**
     * Daftar instruksi yang dipilih.
     */
    public function getInstruksiListAttribute(): array
    {
        return $this->instruksi ?? [];
    }
}