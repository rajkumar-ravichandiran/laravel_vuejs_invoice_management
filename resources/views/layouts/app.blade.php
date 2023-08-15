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
        <link href="{{ asset('assets') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
        <link href="{{ asset('assets') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
        <!-- Argon CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Select2 CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        <link type="text/css" href="{{ asset('argon') }}/css/argon.css" rel="stylesheet">
        <link type="text/css" href="{{ asset('assets') }}/css/custom.css" rel="stylesheet">
        @yield('css')
    </head>
    <body class="{{ $class ?? '' }}">
        @auth()
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @include('layouts.navbars.sidebar')
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
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
        <script src="{{ asset('assets') }}/js/notify.js"></script>
        <script src="{{ asset('assets') }}/js/custom.js"></script>
        <!-- Axios CDN link -->
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <!-- Vue.js CDN link -->
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
        <!-- Select2 -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
        @stack('js')        
        <!-- Argon JS -->
        <script src="{{ asset('argon') }}/js/argon.js?v=1.0.0"></script>
    </body>
</html>