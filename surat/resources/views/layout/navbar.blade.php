<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <img src="{{ asset('adminlte/dist/img/user2-160x160.jpg') }}" class="user-image img-circle"
                    alt="User Image">

                @auth
                    {{ auth()->user()->nama }}
                @endauth
            </a>

            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- User image -->
                <li class="user-header bg-primary">
                    <img src="{{ asset('adminlte/dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">

                    @auth
                        <p class="text-center">
                            {{ auth()->user()->name }}
                        <div>
                            @if (auth()->user()->role === 'admin')
                                <span class="badge badge-primary">Guru</span>
                            @elseif (auth()->user()->role === 'superadmin')
                                <span class="badge badge-info">Admin</span>
                            @else
                                <span class="badge badge-secondary">
                                    {{ auth()->user()->role ?? '-' }}
                                </span>
                            @endif
                        </div>
                        </p>
                    @endauth
                </li>

                <!-- Menu Footer -->
                <li class="user-footer">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="btn btn-danger rounded-pill px-4 py-1 shadow-sm d-inline-flex align-items-center">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            <span class="font-weight-bold">Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>