<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('meta')

    <title>@yield('title', config('app.name'))</title>

    <link rel="icon" type="image/png"   href="{{ asset('casva/img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/perfect-scrollbar/css/perfect-scrollbar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/font-iconmoon/style.css') }}" rel="stylesheet">
    <link href="{{ asset('ui/core/core.css') }}" rel="stylesheet">
    <link href="{{ asset('ui/vendor/vendors.css') }}" rel="stylesheet">
    <link href="{{ asset('ui/layouts/layouts-pack.css') }}" rel="stylesheet">
    <link href="{{ asset('ui/pages.css') }}" rel="stylesheet">
    <link href="{{ asset('ui/apps.css') }}" rel="stylesheet">
    @stack('styles')
    <link href="{{ asset('css/core.css') }}" rel="stylesheet">
</head>
<body>
@yield('content')
<script type="text/javascript" src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/popper.js/popper.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/cookie/plugin.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/autosize/autosize.min.js') }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        // beforeSend: function(){
        //     $("#preloader").show();
        // },
        // complete: function(){
        //     $("#preloader").hide();
        // }
    });
</script>
<script>
// Add the following code if you want the name of the file appear on select
$("#logo").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".logolabel").addClass("selected").html('<font>'+fileName+'</font>');
});

$("#certificate").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".certificatelabel").addClass("selected").html('<font>'+fileName+'</font>');
});

$("#licence").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".licencelabel").addClass("selected").html('<font>'+fileName+'</font>');
});

$("#agreement").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".agreementlabel").addClass("selected").html('<font>'+fileName+'</font>');
});
</script>
@stack('scripts')
<script type="text/javascript" src="{{ asset('js/core.js') }}"></script>
</body>
</html>
