@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.admins.roles.store') }}" method="post">
        @csrf
        @component('component.card', ['title' => 'Роли пользователей: '.trans('admin.creating')])
            @include('admin.admin-roles._form')
            @slot('bottom')
                <div class="px-5 py-3">
                    <a href="{{ route('admin.admins.roles.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                    <button class="btn btn-sm btn-success float-right px-3 ">@lang('admin.create')</button>
                    <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
