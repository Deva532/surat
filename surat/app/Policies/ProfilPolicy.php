<?php

namespace App\Policies;

use App\Models\Profil;
use App\Models\User;

class ProfilPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Profil $profil): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Profil $profil): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Profil $profil): bool
    {
        return false;
    }
}
