@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.seasons.update', $season) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')
        @component('component.card', ['title' => 'Сезоны: '.trans('admin.editing')])
            @include('admin.seasons._form')
            @slot('bottom')
                <div class="px-5 py-3">
                <a href="{{ route('admin.seasons.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.save')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
