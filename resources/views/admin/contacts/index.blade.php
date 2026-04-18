@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.contacts.update') }}" method="post">
        @csrf
        @method('put')
        @component('component.card', ['title' => 'Настройки'])
            @include('admin.contacts._form')
            @slot('bottom')
                <div class="px-5 py-3">
                    <button class="btn btn-sm btn-primary ">@lang('admin.save')</button>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
