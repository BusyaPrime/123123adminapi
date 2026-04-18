@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.company_priorities.update', $priority) }}" method="post">
        @csrf
        @method('put')
        @component('component.card', ['title' => 'Процентная ставка'.': '.trans('admin.editing')])
            @include('admin.company-priorities._form')
            @slot('bottom')

                <div class="px-5 py-3">
                <a href="{{ route('admin.company_priorities.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.save')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
