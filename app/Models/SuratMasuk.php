<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratMasuk extends Model
{
    protected $table = 'surat_masuks';

    protected $fillable = [
        'nomor_agenda',
        'nomor_surat',
        'pengirim',
        'penerima_dituju',
        'perihal',
        'tanggal_surat',
        'tanggal_diterima',
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
            'tanggal_diterima' => 'date',
        ];
    }

    public function kategoriSurat(): BelongsTo
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
    public function disposisi()
    {
        return $this->hasOne(\App\Models\Disposisi::class, 'surat_masuk_id')
            ->latestOfMany();
    }

    public function allDisposisi()
    {
        return $this->hasMany(\App\Models\Disposisi::class, 'surat_masuk_id');
    }

    public function penerimaList()
    {
        return $this->hasMany(SuratMasukPenerima::class, 'surat_masuk_id');
    }

    public function scopeForUser($query, ?User $user = null)
    {
        $user = $user ?: auth()->user();

        if (!$user || $user->isAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->whereHas('penerimaList', function ($p) use ($user) {
                $p->where('user_id', $user->id)
                    ->orWhere('jabatan', $user->jabatan);
            })
            ->orWhereHas('allDisposisi', function ($d) use ($user) {
                $d->where('penerima_id', $user->id);
            })
            ->orWhere('penerima_dituju', 'like', '%' . $user->name . '%')
            ->orWhere('penerima_dituju', 'like', '%' . ($user->jabatan ?: $user->name) . '%');
        });
    }
}
