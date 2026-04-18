@extends('ui.layout')

@section('menu-left')
    @include('admin2.menu-left')
@endsection

@section('top-bar')
    @include('admin2.top-bar')
@endsection

@section('content')
    @yield('center_content')
@endsection

@section('footer')
    @include('admin2.footer')
@endsection
{{--@push('styles')--}}
{{--    <style>--}}
{{--        .cat__menu-left__lock, .cat__menu-left__logo {--}}
{{--            background: #fff;--}}
{{--        }--}}
{{--        .cat__menu-left__logo img {--}}
{{--            filter: unset!important;--}}
{{--        }--}}
{{--    </style>--}}
{{--@endpush--}}
