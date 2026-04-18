@extends('ui.layout')

@section('menu-left')
    @include('admin.menu-left')
@endsection

@section('top-bar')
    @include('admin.top-bar')
@endsection

@section('content')
    @yield('center_content')
@endsection

@section('footer')
    @include('admin.footer')
@endsection
