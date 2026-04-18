@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.sizes.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @component('component.card', ['title' => trans('admin.nav.sizes').': '.trans('admin.creating')])
            @include('admin.sizes._form')
            @slot('bottom')

                <div class="px-5 py-3">
                <a href="{{ route('admin.sizes.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.create')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
