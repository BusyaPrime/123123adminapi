@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.bookings.store') }}" method="post" id="create_from" enctype="multipart/form-data">
        @csrf
        @component('component.card', ['title' => "Добавить Заказ", 'title_class' => 'col-sm-12'])
            @include('admin.bookings._form')
            @slot('bottom')
                <div class="px-5 py-3">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-danger float-left" >@lang('admin.back')</a>
                <button id="submit_button"  class="btn btn-sm btn-primary float-right" >Продолжить</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
