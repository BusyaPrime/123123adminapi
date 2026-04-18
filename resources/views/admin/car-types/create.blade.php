@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.car-types.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @component('component.card', ['title' => trans('admin.nav.car-types').': '.trans('admin.creating')])
            @include('admin.car-types._form', ['carType' => null])
            @slot('bottom')

                <div class="px-5 py-3">
                <a href="{{ route('admin.car-types.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.create')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
