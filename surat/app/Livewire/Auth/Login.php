<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    public function mount()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $key = Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan login. Coba lagi nanti.',
            ]);
        }

        if (! Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            RateLimiter::hit($key, 60);
            $this->addError('email', 'Email atau password salah.');
            return;
        }

        RateLimiter::clear($key);
        request()->session()->regenerate();

        return $this->redirectByRole();
    }

    private function redirectByRole()
    {
        $user = Auth::user();

        if ($user->role === 'superadmin') {
            return redirect()->route('superadmin.suratmasuk.index');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.suratmasuk.index');
        }

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layout.guest');
    }
}
