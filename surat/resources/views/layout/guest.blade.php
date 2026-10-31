<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sekolah | Login</title>

    @include('layout.style')
    @livewireStyles
</head>

<body class="hold-transition login-page">

    <main>
        {{ $slot }}
    </main>

    @include('layout.script')
    @livewireScripts

</body>
</html>