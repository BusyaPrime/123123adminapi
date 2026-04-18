@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.commission-rates.store') }}" method="post">
        @csrf
        @component('component.card', ['title' => 'Процентная ставка'.': '.trans('admin.creating')])
            @include('admin.commission-rates._form')
            @slot('bottom')
                <div class="px-5 py-3">
                <a href="{{ route('admin.commission-rates.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.create')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
