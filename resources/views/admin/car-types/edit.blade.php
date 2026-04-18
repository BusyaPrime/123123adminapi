@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.car-types.update', $carType) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')
        @component('component.card', ['title' => trans('admin.nav.car-types').': '.trans('admin.editing')])
            @include('admin.car-types._form')
            @slot('bottom')
                <div class="px-5 py-3">
                <a href="{{ route('admin.car-types.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.save')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
