@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Детали транзакции', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')

{{--            @component('component.modal', ['id' => 'filter', 'class' => 'btn btn-sm btn-secondary ml-2'])--}}
{{--                @slot('label')--}}
{{--                    <i class="icmn-filter"></i>--}}
{{--                @endslot--}}
{{--                @slot('title')--}}
{{--                    @lang('admin.filters')--}}
{{--                @endslot--}}

{{--                <div id="filters">--}}
{{--                    <form action="{{ route('admin.Дs.index') }}" method="get">--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="id">--}}
{{--                                        <small>@lang('validation.attributes.id')</small>--}}
{{--                                    </label>--}}
{{--                                    <input class="form-control input-sm" name="id" id="id" type="number" step="1" min="1" value="{{ $filters['id'] ?? '' }}"/>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="username">--}}
{{--                                        <small>@lang('validation.attributes.username')</small>--}}
{{--                                    </label>--}}
{{--                                    <input class="form-control input-sm" name="username" id="username" type="text"  value="{{ $filters['username'] ?? '' }}"/>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="name">--}}
{{--                                        <small>ФИО</small>--}}
{{--                                    </label>--}}
{{--                                    <input class="form-control input-sm" name="name" id="name" type="text"  value="{{ $filters['name'] ?? '' }}"/>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="active">--}}
{{--                                        <small>@lang('validation.attributes.active')</small>--}}
{{--                                    </label>--}}
{{--                                    <select name="active" id="active" class="form-control input-sm">--}}
{{--                                        <option value=""></option>--}}
{{--                                        <option value="1" {{ (isset($filters['active']) && $filters['active'] == 1) ? 'selected': ''}}>@lang('admin.active')</option>--}}
{{--                                        <option value="0" {{ (isset($filters['active']) && $filters['active'] == 0) ? 'selected': ''}}>@lang('admin.not_active')</option>--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="role">--}}
{{--                                        <small>Роль</small>--}}
{{--                                    </label>--}}
{{--                                    <select name="role" id="role" class="form-control input-sm">--}}
{{--                                        <option value=""></option>--}}
{{--                                        <option value="users" {{ (isset($filters['role']) && $filters['role'] == 'users') ? 'selected': ''}}>Пользователь</option>--}}
{{--                                        <option value="cars" {{ (isset($filters['role']) && $filters['role'] == 'cars') ? 'selected': ''}}>Водитель</option>--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="sort">--}}
{{--                                        <small>@lang('admin.sort')</small>--}}
{{--                                    </label>--}}
{{--                                    <select name="sort" id="sort" class="form-control input-sm">--}}
{{--                                        <option value=""></option>--}}
{{--                                        <option value="id" {{ (isset($filters['sort']) && $filters['sort'] == 'id') ? 'selected': ''}}>@lang('validation.attributes.id') (@lang('admin.asc'))</option>--}}
{{--                                        <option value="-id" {{ (isset($filters['sort']) && $filters['sort'] == '-id') ? 'selected': ''}}>@lang('validation.attributes.id') (@lang('admin.desc'))</option>--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="float-left">--}}
{{--                                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-danger">@lang('admin.filters_reset')</a>--}}
{{--                                    <button class="btn btn-sm btn-success">@lang('admin.filters_apply')</button>--}}
{{--                                </div>--}}
{{--                                <div class="clearfix"></div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            @endcomponent--}}

{{--<div class="row col-sm-6 justify-content-end">--}}
{{--    <button type="button" class="btn btn-default mr-3 " onclick="window.print()">--}}
{{--        <i class="nav-icon fas fa-print" ></i>--}}
{{--        Печать--}}
{{--    </button>--}}
{{--    <a href="{{ route('admin.transactions.index') }}" class="btn btn-info">Назад</a>--}}
{{--        <a href="{{ route('admin.users.create') }}" class="btn btn-info ml-2">--}}
{{--            <span class="d-none d-sm-inline-block">@lang('admin.create')</span> <i class="icmn-plus"><!-- --></i>--}}
{{--        </a>--}}
{{--</div>--}}
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
                        <input type="text" class="form-control" placeholder="Компания: {{ $transaction->company_id ? ($transaction->company->title ?? 'Не найдена') :'Самозанятый' }}" disabled style="background: transparent">
                        <span class="input-group-text {{$transaction->company_id ? '':'p-0'}}">
                            @if($transaction->company_id && $transaction->company)
                                <a href="{{ route('admin.companies.show', $transaction->company_id) }}" class="">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Тип транзакции: {{ $transaction->type == 'refill' ? 'Пополнение': 'Списание' }}" disabled style="background: transparent">
                    </div>
                </div>


                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Водитель: {{ (optional(optional($transaction->user)->profile)->surname ?? '').' '.(optional(optional($transaction->user)->profile)->name ?? '').' '.(optional(optional($transaction->user)->profile)->middle_name ?? '') }}" disabled style="background: transparent">
                        <span class="input-group-text {{$transaction->user_id ? '':'p-0'}}">
                            @if($transaction->user_id && $transaction->user)
                                @if(isset ($transaction->user->car))
                                    <a href="{{ route('admin.cars.show', $transaction->user->car->id ?? 0) }}" class="">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @else
                                    <a href="{{ route('admin.users.show', $transaction->user_id ?? 0) }}" class="">
                                        <i class="fas fa-eye"></i>
                                    </a>
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
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-info ">Назад</a>
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
