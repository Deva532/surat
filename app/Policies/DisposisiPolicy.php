<?php

namespace App\Policies;

use App\Models\Disposisi;
use App\Models\User;

class DisposisiPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Disposisi $disposisi): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $disposisi->penerima_id === $user->id || $disposisi->pengirim_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Disposisi $disposisi): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Jabatan penerima hanya boleh update status / catatan tindak lanjut miliknya
        return $disposisi->penerima_id === $user->id;
    }

    public function delete(User $user, Disposisi $disposisi): bool
    {
        return $user->isAdmin();
    }
}
