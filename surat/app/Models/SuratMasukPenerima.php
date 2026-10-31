<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratMasukPenerima extends Model
{
    protected $table = 'surat_masuk_penerima';

    protected $fillable = [
        'surat_masuk_id',
        'user_id',
        'jabatan',
        'status',
        'catatan',
        'dibaca_pada',
        'selesai_pada',
    ];

    protected function casts(): array
    {
        return [
            'dibaca_pada' => 'datetime',
            'selesai_pada' => 'datetime',
        ];
    }

    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
