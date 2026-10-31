<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    {{-- <a href="{{ url('/') }}" class="brand-link">
        @if ($profil && $profil->logo)
        <img src="{{ asset('storage/' . $profil->logo) }}" class="brand-image img-circle elevation-3"
            style="opacity:.8; object-fit: contain;">
        @else
        <img src="{{ asset('adminlte/dist/img/AdminLTELogo.png') }}" class="brand-image img-circle elevation-3"
            style="opacity:.8; object-fit: contain;">
        @endif
        <span class="brand-text font-weight-bold" style="font-size: 14px; white-space: normal; line-height: 1.2;">
            {{ $profil->nama_sekolah ?? 'Nama Sekolah' }}
        </span>

    </a> --}}


    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class 
                            with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a wire:navigate href="#" class="nav-link 
                                    @yield('menuDashboard')">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    {{-- <a wire:navigate href="{{ route('log.index') }}" class="nav-link
                                            @yield('menuLogTransaksi')">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Log Transaksi</p>
                    </a> --}}
                </li>
                {{-- @if(auth()->user()->role === 'superadmin') --}}
                <li class="nav-header">SUPER ADMIN</li>
                <li class="nav-item">
                    <a wire:navigate href="{{route('superadmin.profile.index')}}" class="nav-link 
                                                @yield('menuSuperadminProfile')">
                        <i class="nav-icon fas fa-sitemap"></i>
                        <p>
                            Profil
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a wire:navigate href="{{route('superadmin.user.index')}}" class="nav-link 
                                                @yield('menuSuperadminUser')">
                        <i class="nav-icon far fa-user"></i>
                        <p>
                            User
                        </p>
                    </a>
                </li>
                <li class="nav-header">ADMINITRASI</li>
                <li class="nav-item">
                    <a wire:navigate href="{{route('superadmin.suratmasuk.index')}}" class="nav-link 
                                                @yield('menuSuperadminSuratMasuk')">
                        <i class="nav-icon fas fa-envelope-open"></i>
                        <p>
                            Surat Masuk
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a wire:navigate href="{{ route('superadmin.kategorisurat.index') }}" class="nav-link 
                                                @yield('menuSuperadminKategoriSurat')">
                        <i class="nav-icon fas fa-at"></i>
                        <p>
                            Kategori Surat
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a wire:navigate href="{{ route('superadmin.desposisi.index') }}" class="nav-link 
                                                @yield('menuSuperadminDesposisi')">
                        <i class="nav-icon fas fa-inbox"></i>
                        <p>
                            Desposit Surat
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a wire:navigate href="{{ route('superadmin.suratkeluar.index') }}" class="nav-link 
                                                @yield('menuSuperadminSuratKeluar')">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>
                            Surat Keluar
                        </p>
                    </a>
                </li>
                {{-- @endif --}}
                {{-- @if(auth()->user()->role === 'admin') --}}
                {{-- {{-- <li class="nav-header">ADMINITRASI</li> --}}
                <li class="nav-item">
                    <a wire:navigate href="#" class="nav-link 
                                            @yield('menuAdminBarang')">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>
                            Surat Keluar
                        </p>
                    </a>
                </li>
                {{-- @endif --}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>