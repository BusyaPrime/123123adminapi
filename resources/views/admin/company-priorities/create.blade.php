@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.company_priorities.store') }}" method="post">
        @csrf
        @component('component.card', ['title' => 'Приоритет юр. лиц'.': '.trans('admin.creating')])
            @include('admin.company-priorities._form')
            @slot('bottom')
                <div class="px-5 py-3">
                <a href="{{ route('admin.company_priorities.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.create')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
