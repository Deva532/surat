<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nip',
        'email',
        'password',
        'role',
        'jabatan',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin'], true);
    }

    public function isJabatan(): bool
    {
        return !empty($this->jabatan) || $this->role === 'jabatan';
    }

    public function getJabatanName(): ?string
    {
        return $this->jabatan ?: ($this->isSuperadmin() ? 'Administrator' : 'Staf');
    }

    public function penerimaSurats()
    {
        return $this->hasMany(SuratMasukPenerima::class, 'user_id');
    }

    public function disposisiDiterima()
    {
        return $this->hasMany(Disposisi::class, 'penerima_id');
    }

    public function disposisiDikirim()
    {
        return $this->hasMany(Disposisi::class, 'pengirim_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
