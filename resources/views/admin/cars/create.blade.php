@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.cars.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @component('component.card', ['title' => 'Водители'.': '.trans('admin.creating')])
            @include('admin.cars._form')
            @slot('bottom')
                <div class="px-5 py-3">
                    <a href="{{ route('admin.cars.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                    <button class="btn btn-sm btn-success float-right px-3 ">@lang('admin.create')</button>
                    <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
