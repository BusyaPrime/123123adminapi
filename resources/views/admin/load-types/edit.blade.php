@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.load-types.update', $loadType) }}" method="post">
        @csrf
        @method('put')
        @component('component.card', ['title' => trans('admin.nav.load-types').': '.trans('admin.editing')])
            @include('admin.load-types._form')
            @slot('bottom')

                <div class="px-5 py-3">
                <a href="{{ route('admin.load-types.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.save')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
