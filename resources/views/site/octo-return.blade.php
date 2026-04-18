<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/png"   href="{{ asset('casva/img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="{{ asset('css/adminlte.css') }}" rel="stylesheet">
</head>
<body class="bg-white">
    <div class="row">
        <div class="col ">
            <div class="my-5 text-center">
                <h1>Спасибо что выбрали нас</h1>
            </div>
        </div>
    </div>
</body>
</html>
