<?php

namespace App\Policies;

use App\Models\KategoriSurat;
use App\Models\User;

class KategoriSuratPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, KategoriSurat $kategoriSurat): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, KategoriSurat $kategoriSurat): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, KategoriSurat $kategoriSurat): bool
    {
        return $user->isAdmin();
    }
}
