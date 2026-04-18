@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.tickets.update', $ticket) }}" method="post">
        @csrf
        @method('put')
        @component('component.card', ['title' => 'Предл. и жалоб: '.trans('admin.editing')])

            <div class="form-group row ">
                <label class="col-md-3 text-md-right col-form-label-sm" >Дата</label>
                <div class="col-md-6">
                    <input type="text" class="form-control input-sm" disabled value="{{ $ticket->created_at ? $ticket->created_at->format('d.m.Y H:i'): '--' }}"  >
                </div>
            </div>

            <div class="form-group row ">
                <label class="col-md-3 text-md-right col-form-label-sm" >ID пользователя</label>
                <div class="col-md-6">
                    <input type="text" class="form-control input-sm" disabled value="{{ $ticket->user_id ?? '' }}"  >
                </div>
            </div>

            <div class="form-group row ">
                <label class="col-md-3 text-md-right col-form-label-sm" >ФИО</label>
                <div class="col-md-6">
                    <input type="text" class="form-control input-sm" disabled value="{{ $ticket->user_name ?? '' }}"  >
                </div>
            </div>

            <div class="form-group row ">
                <label class="col-md-3 text-md-right col-form-label-sm" >Тип пользователя</label>
                <div class="col-md-6">
                    <input type="text" class="form-control input-sm" disabled value="{{ $ticket->user_type == 'driver' ? 'Водитель': 'Клиент' }}"  >
                </div>
            </div>

            <div class="form-group row ">
                <label class="col-md-3 text-md-right col-form-label-sm" >Тема</label>
                <div class="col-md-6">
                    <input type="text" class="form-control input-sm" disabled value="{{ $ticket->subject ?? '' }}"  >
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-md-3 text-md-right col-form-label-sm" >Текст</label>
                <div class="col-md-6">
                    <div class="border p-2">
                        {{ $ticket->text ?? '' }}
                    </div>
                </div>
            </div>
            @if($ticket->file)
            <div class="form-group row ">
                <label class="col-md-3 text-md-right col-form-label-sm" >Прикрепленный файл</label>
                <div class="col-md-6">
                    <div class="border p-2">
                        <a href="{{ asset('uploads/tickets/'.$ticket->file) }}" target="_blank" class="Скачать">Открыть</a>
                    </div>
                </div>
            </div>
            @endif

            @include('admin.tickets._form')



        @slot('bottom')

                <div class="px-5 py-3">
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.save')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
