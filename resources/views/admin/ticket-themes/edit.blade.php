@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.ticket-themes.update', $ticket_theme) }}" method="post">
        @csrf
        @method('put')
        @component('component.card', ['title' => 'Темы предл. и жалоб: '.trans('admin.editing')])
            @include('admin.ticket-themes._form')
            @slot('bottom')

                <div class="px-5 py-3">
                <a href="{{ route('admin.ticket-themes.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.save')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
