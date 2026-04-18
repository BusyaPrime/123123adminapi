@extends('admin2.layout')

@section('center_content')
    <form action="{{ route('admin2.bookings.book') }}" method="post" id="create_from" enctype="multipart/form-data">
        @csrf
        @component('component.card', ['title' => "Добавить Заказ", 'title_class' => 'col-sm-12'])
            @include('admin2.bookings._form_store')
            @slot('bottom')
                <div class="px-5 py-3">
                    <a href="{{ route('admin2.bookings.index') }}" class="btn btn-sm btn-danger float-left" >Отмена</a>
                    @if($rates->isNotEmpty())
                    <button id="submit_button"  class="btn btn-sm btn-primary float-right" >Разместить заказ</button>
                    @endif
                    <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
