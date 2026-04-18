@extends('admin2.layout')

@section('center_content')
    @component('component.card', ['title' => 'Детали транзакции', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')

        @endslot

        <form class="border rounded-lg p-3">
            <div class="form-group">
                <label class="text-primary">
                    Информация
                </label>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" style="background: transparent" placeholder="ID транзакции: {{ $transaction->id ?? '' }}" disabled>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Компания: {{ $transaction->company_id ? $transaction->company->title :'Самозанятый' }}" disabled style="background: transparent">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Тип транзакции: {{ $transaction->type == 'refill' ? 'Пополнение': 'Списание' }}" disabled style="background: transparent">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Водитель: {{ ($transaction->user->profile->surname ?? '').' '.($transaction->user->profile->name ?? '').' '.($transaction->user->profile->middle_name ?? '') }}" disabled style="background: transparent">
                        <span class="input-group-text {{$transaction->user_id ? '':'p-0'}}">
                            @if($transaction->user_id)
                                @if(isset ($transaction->user) && isset($transaction->user->car))
                                    <a href="{{ route('admin2.cars.show', $transaction->user->car->id ?? 0) }}" class="">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @else
                                    <span class="px-2">Удален</span>
                                @endif
                            @endif
                        </span>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <div  class="form-control rounded-0 text-muted" disabled style="background: transparent">
                            Сумма транзакции: {!! $transaction->type == 'refill'? '<span class="text-success">+'.$transaction->amount.'</span>': '<span class="text-danger">-'.$transaction->amount.'</span>' !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <div  class="form-control rounded-0 text-muted" disabled style="background: transparent">
                            Дата: {{ $transaction->created_at->format('d.m.Y H:i') }}
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                        <div  class="form-control rounded-0 text-muted" disabled style="background: transparent">
                            Основание: {{ $transaction->description ?? 'Не указано' }}
                        </div>
                    </div>
                </div>
{{--                <div class="col-sm-6">--}}
{{--                    <div class="form-group">--}}
{{--                        <input type="text" class="form-control" placeholder="ДШВ заказа: --" disabled style="background: transparent">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-sm-6">--}}
{{--                    <div class="form-group">--}}
{{--                        <input type="text" class="form-control" placeholder="Стоимость груза: --" disabled style="background: transparent">--}}
{{--                    </div>--}}
{{--                </div>--}}

            </div>


            <div class="add-company__2 mt-3 d-flex align-items-center justify-content-between">
                <a href="{{ route('admin2.transactions.index') }}" class="btn btn-info ">Назад</a>
            </div>
        </form>


{{--        <form class="border rounded-lg p-3">--}}


{{--            <div class="row">--}}
{{--                <div class="col-sm-6">--}}
{{--                    <div class="form-group  {!! $errors->first('title', 'has-danger')!!}">--}}
{{--                        <label class=" text-md-right text-secondary" for="title">Наименование компании</label>--}}
{{--                        <div class="">--}}
{{--                            <input type="text" name="title" class="form-control input-sm" id="title"--}}
{{--                                   value="{{ old('title', '') }}" >--}}
{{--                            {!! $errors->first('title', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-sm-6">--}}
{{--                    <div class="form-group  {!! $errors->first('director_name', 'has-danger')!!}">--}}
{{--                        <label class=" text-md-right text-secondary" for="director_name">Сумма поступления</label>--}}
{{--                        <div class="">--}}
{{--                            <input type="text" name="director_name" class="form-control input-sm" id="director_name"--}}
{{--                                   value="{{ old('director_name', '') }}" >--}}
{{--                            {!! $errors->first('director_name', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-sm-6">--}}
{{--                    <div class="form-group  {!! $errors->first('director_name', 'has-danger')!!}">--}}
{{--                        <label class=" text-md-right text-secondary" for="director_name">Ф.И.О водителя</label>--}}
{{--                        <div class="">--}}
{{--                            <input type="text" name="director_name" class="form-control input-sm" id="director_name"--}}
{{--                                   value="{{ old('director_name', '') }}" >--}}
{{--                            {!! $errors->first('director_name', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-sm-6">--}}
{{--                    <div class="form-group  {!! $errors->first('director_name', 'has-danger')!!}">--}}
{{--                        <label class=" text-md-right text-secondary" for="director_name">Ф.И.О пользователя</label>--}}
{{--                        <div class="">--}}
{{--                            <input type="text" name="director_name" class="form-control input-sm" id="director_name"--}}
{{--                                   value="{{ old('director_name', '') }}" >--}}
{{--                            {!! $errors->first('director_name', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}


{{--                        <div class="add-company__2 mt-3 d-flex align-items-center justify-content-between">--}}
{{--                            <a href="#" >&nbsp;</a>--}}
{{--                            <a href="#" class="btn btn-info ">Сохранить</a>--}}
{{--                        </div>--}}
{{--        </form>--}}
    @endcomponent
@endsection
