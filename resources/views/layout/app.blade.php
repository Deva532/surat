@php
    // $profil = \App\Models\Profil::first();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-SURAT SUGIHAN PRIMA | @yield('title')</title>

    @include('layout.style')
    @livewireStyles
</head>

<body class="hold-transition sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->
        @include('layout.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->

        @include('layout.sidebar')

        <!-- Content Wrapper. Contains page content -->
        @yield('content')
        <!-- /.content-wrapper -->

        @include('layout.footer')


    </div>
    <!-- ./wrapper -->

    @include('layout.script')
    @livewireScripts
</body>

</html>
