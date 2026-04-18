@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.cancel-reasons.store') }}" method="post">
        @csrf
        @component('component.card', ['title' => 'Причины отмены: '.trans('admin.creating')])
            @include('admin.cancel-reasons._form')
            @slot('bottom')
                <div class="px-5 py-3">
                <a href="{{ route('admin.cancel-reasons.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.create')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
