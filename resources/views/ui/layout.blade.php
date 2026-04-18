<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('meta')

    <title>@yield('title', config('app.name'))</title>

    <link rel="icon" type="image/png"   href="{{ asset('casva/img/logo_favicon.png') }}">

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('casva/dist/css/adminlte.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/summernote/summernote-bs4.min.css') }}">

    @php
        $customAdminCssVersion = @filemtime(public_path('css/adminlte.css')) ?: '1';
        $coreCssVersion = @filemtime(public_path('css/core.css')) ?: '1';
    @endphp
    @stack('styles')
    <link href="{{ asset('css/adminlte.css').'?v='.$customAdminCssVersion }}" rel="stylesheet">
    <link href="{{ asset('css/core.css').'?v='.$coreCssVersion }}" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">

{{--    <!-- Preloader -->--}}
{{--    <div class="preloader flex-column justify-content-center align-items-center">--}}
{{--        <img class="animation__shake" src="{{ asset('casva/img/logo.png') }}" alt="Casva">--}}
{{--    </div>--}}

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    @yield('top-bar')
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @yield('menu-left')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @if (session()->has('success'))
            @component('component.alert', ['type' => 'success'])
                {!! session('success') !!}
            @endcomponent
        @endif
        @if (session()->has('warning'))
            @component('component.alert', ['type' => 'warning'])
                {!! session('warning') !!}
            @endcomponent
        @endif
        @if (session()->has('danger'))
            @component('component.alert', ['type' => 'danger'])
                {!! session('danger')  !!}
            @endcomponent
        @endif

        @yield('content')
    </div>
</div>

<!-- jQuery -->
<script src="{{ asset('casva/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('casva/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('casva/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('casva/plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('casva/plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('casva/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('casva/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('casva/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('casva/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('casva/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('casva/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('casva/plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('casva/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('casva/dist/js/adminlte.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('casva/dist/js/demo.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('casva/dist/js/pages/dashboard.js') }}"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function(){
            $("#preloader").show();
        },
        complete: function(){
            $("#preloader").hide();
        }
    });
</script>
@stack('scripts')
<script type="text/javascript" src="{{ asset('js/core.js') }}"></script>
</body>
</html>
