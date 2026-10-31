<?php

namespace App\Policies;

use App\Models\SuratKeluar;
use App\Models\User;

class SuratKeluarPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SuratKeluar $suratKeluar): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, SuratKeluar $suratKeluar): bool
    {
        return $user->isAdmin() || $suratKeluar->created_by === $user->id;
    }

    public function delete(User $user, SuratKeluar $suratKeluar): bool
    {
        return $user->isAdmin();
    }
}
