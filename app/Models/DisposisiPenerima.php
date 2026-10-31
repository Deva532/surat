<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisposisiPenerima extends Model
{
    protected $table = 'disposisi_penerima';

    protected $fillable = [
        'disposisi_id',
        'user_id',
        'jabatan',
        'status',
        'catatan_tindak_lanjut',
        'dibaca_pada',
        'selesai_pada',
    ];

    protected function casts(): array
    {
        return [
            'dibaca_pada'  => 'datetime',
            'selesai_pada' => 'datetime',
        ];
    }

    public function disposisi(): BelongsTo
    {
        return $this->belongsTo(Disposisi::class, 'disposisi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
