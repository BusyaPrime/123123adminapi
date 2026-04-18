@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.admins.store') }}" method="post">
        @csrf
        @component('component.card', ['title' => trans('admin.nav.admins').': '.trans('admin.creating')])
            @include('admin.admins._form')
            @slot('bottom')

                <div class="px-5 py-3">
                <a href="{{ route('admin.admins.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.create')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
