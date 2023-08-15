<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Invoice Management App') }}</title>
        <!-- Favicon -->
        <link href="{{ asset('assets') }}/img/brand/favicon.png" rel="icon" type="image/png">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets') }}/img/brand/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets') }}/img/brand/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets') }}/img/brand/favicon-16x16.png">
        <link rel="manifest" href="{{ asset('assets') }}/img/brand/site.webmanifest">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
        <!-- Extra details for Live View on GitHub Pages -->

        <!-- Icons -->
        <link href="{{ asset('argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
        <link href="{{ asset('argon') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
        <!-- Argon CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link type="text/css" href="{{ asset('argon') }}/css/argon.css?v=1.0.0" rel="stylesheet">
        @yield('css')
    </head>
    <body class="{{ $class ?? '' }}">
        @auth()
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endauth
        
        <div class="main-content">
            @include('layouts.navbars.navbar')
            @yield('content')
        </div>

        @guest()
            @include('layouts.footers.guest')
        @endguest

        <script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>
        <script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('assets') }}/js/custom.js"></script>
        @stack('js')
        
        <!-- Argon JS -->
        <script src="{{ asset('argon') }}/js/argon.js?v=1.0.0"></script>
    </body>
</html>