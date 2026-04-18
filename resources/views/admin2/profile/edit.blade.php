@extends('admin2.layout')

@section('center_content')
    <form action="{{ route('admin2.profile.update') }}" method="post">
        @csrf
        @method('put')
        @component('component.card', ['title' => trans('admin.profile')])
            @include('admin2.profile._form')
            @slot('bottom')
{{--                <a href="{{ route('partner.home') }}" class="btn btn-sm btn-danger float-left">@lang('partner.nav.home')</a>--}}
            <div class="text-center">
                <button class="btn btn-sm btn-primary d-inline-block">@lang('admin.save')</button>
            </div>
            @endslot
        @endcomponent
    </form>
@endsection
