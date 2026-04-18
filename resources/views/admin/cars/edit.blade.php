@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.cars.update', $car) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')
        @component('component.card', ['title' => 'Водители'.': '.trans('admin.editing')])
            @include('admin.cars._form')
            @slot('bottom')
                <div class="px-5 py-3">
                    <a href="{{ route('admin.cars.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                    <button class="btn btn-sm btn-success float-right px-3 ">@lang('admin.save')</button>
                    <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
