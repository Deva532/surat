<div class="login-box mx-auto mt-5" style="max-width: 400px;">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <h3 class="font-weight-bold">
                Rekap Backup Surat Menyurat
            </h3>
            <p class="text-muted mb-0">Silakan login untuk melanjutkan</p>
        </div>

        <div class="card-body">
            <form wire:submit="login">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        wire:model="email"
                        placeholder="Masukkan email"
                        autocomplete="username"
                    >
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        wire:model="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                    >
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="remember"
                            wire:model="remember"
                        >
                        <label class="custom-control-label" for="remember">
                            Ingat saya
                        </label>
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary btn-block"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Login</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </form>
        </div>
    </div>
</div>