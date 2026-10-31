<?php

namespace App\Policies;

use App\Models\SuratMasuk;
use App\Models\User;

class SuratMasukPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SuratMasuk $suratMasuk): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Cek apakah user atau jabatan user menjadi penerima surat
        $isPenerimaList = $suratMasuk->penerimaList()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('jabatan', $user->jabatan);
            })->exists();

        if ($isPenerimaList) {
            return true;
        }

        // Cek apakah user menjadi penerima disposisi
        $isPenerimaDisposisi = $suratMasuk->allDisposisi()
            ->where('penerima_id', $user->id)
            ->exists();

        if ($isPenerimaDisposisi) {
            return true;
        }

        // Fallback cek penerima_dituju
        if (!empty($suratMasuk->penerima_dituju)) {
            if (str_contains($suratMasuk->penerima_dituju, $user->name)) {
                return true;
            }
            if ($user->jabatan && str_contains($suratMasuk->penerima_dituju, $user->jabatan)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SuratMasuk $suratMasuk): bool
    {
        return $user->isSuperadmin();
    }
}
