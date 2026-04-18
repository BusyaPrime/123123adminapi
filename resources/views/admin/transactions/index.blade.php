@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Транзакции', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')

{{--            @component('component.modal', ['id' => 'filter', 'class' => 'btn btn-sm btn-secondary ml-2'])--}}
{{--                @slot('label')--}}
{{--                    <i class="icmn-filter"></i>--}}
{{--                @endslot--}}
{{--                @slot('title')--}}
{{--                    @lang('admin.filters')--}}
{{--                @endslot--}}

{{--                <div id="filters">--}}
{{--                    <form action="{{ route('admin.users.index') }}" method="get">--}}
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
<div class="row col-sm-6 justify-content-end">
{{--    <button type="button" class="btn btn-default " onclick="window.print()">--}}
{{--        <i class="nav-icon fas fa-print" ></i>--}}
{{--        Печать--}}
{{--    </button>--}}
{{--    <button type="button" class="btn btn-default  ml-2">Экспорт</button>--}}
{{--    <button type="button" class="btn btn-default  ml-2">Импорт</button>--}}
{{--    <button type="button" class="btn btn-default  ml-2" id="open_filter">--}}
{{--        <span class=" ml-2">Фильтр</span>--}}
{{--        <i class="fas fa-chevron-down text-black pl-2 pr-2" style="color: #2b2b2b!important;"></i>--}}
{{--    </button>--}}
{{--    <button type="button" class="btn btn-default  ml-2" id="close_filter" style="display: none;">--}}
{{--        <span class="text-secondary ml-2">Фильтр</span>--}}
{{--        <i class="fas fa-chevron-up text-black pl-2 pr-2" ></i>--}}
{{--    </button>--}}
{{--    <a href="{{ route('admin.users.create') }}" class="btn btn-info ml-2">--}}
{{--        <span class="d-none d-sm-inline-block">@lang('admin.create')</span> <i class="icmn-plus"><!-- --></i>--}}
{{--    </a>--}}
<!-- Button trigger modal -->
    <div class="dropdown">
        <button class="btn btn-default mr-2 dropdown-toggle"
                type="button" id="dropdownMenuStore"
                data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Задолженность
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuStore">
            <a class="dropdown-item" target="_blank" href="{{ route('admin.transactions.debts.companies') }}" >Компании</a>
            <a class="dropdown-item" target="_blank" href="{{ route('admin.transactions.debts.users') }}" >Самозанятые</a>
        </div>
    </div>
    <div class="dropdown">
        <button class="btn btn-success dropdown-toggle"
                type="button" id="dropdownMenuStore"
                data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Добавить транзакцию
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuStore">
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#CompanyModal">Компания</a>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#UserModal">Самозанятый</a>
            <a class="dropdown-item" href="{{ route('admin.transactions.import-form') }}"  >Импорт</a>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="CompanyModal" tabindex="-1" aria-labelledby="CompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CompanyModalLabel">Новая транзакция компании</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.transactions.store-company') }}" method="post">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="inn">ИНН компании</label>
                                    <input type="text" class="form-control" name="inn" id="inn" placeholder="Введите ИНН компании" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="type">Тип транзакции</label>
                                    <select name="type" class="form-control" id="type" required>
                                        <option value="refill">Пополнение</option>
                                        <option value="debt">Списание</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="amount">Сумма</label>
                                    <input type="number" class="form-control" min="0" step="1" name="amount" id="amount" placeholder="Введите сумму" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Основание</label>
                                    <textarea type="text" class="form-control" name="description" id="description" placeholder="На каком основании транзакция" required></textarea>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button class="btn btn-primary">Добавить</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="UserModal" tabindex="-1" aria-labelledby="UserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="UserModalLabel">Новая транзакция водителя</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.transactions.store-user') }}" method="post">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="transaction_user_id">ID Водителя</label>
                                    <input type="text" class="form-control" name="transaction_user_id" id="transaction_user_id" placeholder="Введите ID Водителя" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="type">Тип транзакции</label>
                                    <select name="type" class="form-control" id="type" required>
                                        <option value="refill">Пополнение</option>
                                        <option value="debt">Списание</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="amount">Сумма</label>
                                    <input type="number" class="form-control" min="0" step="1" name="amount" id="amount" placeholder="Введите сумму" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Основание</label>
                                    <textarea type="text" class="form-control" name="description" id="description" placeholder="На каком основании транзакция" required></textarea>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button class="btn btn-primary">Добавить</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.transactions.export', $filters) }}" class="btn btn-default mb-2 ml-2">
        <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
    </a>

        @if($filters['id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['type'] != '' || $filters['date_start'] != ''|| $filters['date_end'] != '')
            <button type="button" class="btn btn-default  ml-2" id="open_filter" style="display: none;">
                <span class=" ml-2">Фильтр</span>
                <i class="fas fa-chevron-down text-black pl-2 pr-2" style="color: #2b2b2b!important;"></i>
            </button>
            <button type="button" class="btn btn-default  ml-2" id="close_filter" style="">
                <span class="text-secondary ml-2">Фильтр</span>
                <i class="fas fa-chevron-up text-black pl-2 pr-2" ></i>
            </button>
        @else
            <button type="button" class="btn btn-default  ml-2" id="open_filter">
                <span class=" ml-2">Фильтр</span>
                <i class="fas fa-chevron-down text-black pl-2 pr-2" style="color: #2b2b2b!important;"></i>
            </button>
            <button type="button" class="btn btn-default  ml-2" id="close_filter" style="display: none;">
                <span class="text-secondary ml-2">Фильтр</span>
                <i class="fas fa-chevron-up text-black pl-2 pr-2" ></i>
            </button>
        @endif



</div>

        @endslot
        @slot('filters')
            <section class="content" id="filter_block" style="{{ $filters['id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['type'] != '' || $filters['date_start'] != ''|| $filters['date_end'] != '' ?'':'display: none' }}">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="{{ route('admin.transactions.index') }}" id="filter_block_form">
                                <div class="border rounded p-3">
                                    <div class="form-group">

                                        <div class="row justify-content-between">

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="id" value="{{ $filters['id'] ?? '' }}" class="form-control" placeholder="Поиск по ID транзакции">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="user_id" value="{{ $filters['user_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Водителя">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="company_id" value="{{ $filters['company_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Компании">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="type">
                                                        <option value="">Все типы транзакции</option>
                                                        <option value="refill" {{ $filters['type'] == 'refill'? 'selected':'' }}>Пополнения</option>
                                                        <option value="debt" {{ $filters['type'] == 'debt'? 'selected':'' }}>Списания</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row justify-content-between">

                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="date" name="date_start" value="{{ $filters['date_start'] ?? '' }}" class="form-control" >
                                                    <span class="input-group-text">
                                                        &#10141
                                                    </span>
                                                    <input type="date" name="date_end" value="{{ $filters['date_end'] ?? '' }}" class="form-control" >
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="sort">
                                                        <option value="-id" {{ $filters['sort'] == '-id'? 'selected':'' }}>Сначала новые</option>
                                                        <option value="id" {{ $filters['sort'] == 'id'? 'selected':'' }}>Сначала старые</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row align-items-center justify-content-end">
                                            <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                <a href="{{ route('admin.transactions.index') }}" class="btn btn-danger mr-2" style="{{ $filters['id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['type'] != '' || $filters['date_start'] != ''|| $filters['date_end'] != '' ?'':'display: none' }};">Сбросить фильтры</a>
                                                <a onclick="$('#filter_block_form').submit();" class="btn btn-info">Применить</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        @endslot

        @if($transactions->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">@lang('validation.attributes.id')</th>
                            <th class="table__th">Водитель</th>
                            <th class="table__th">Компания</th>
                            <th class="table__th">Тип транзакции</th>
                            <th class="table__th">Основание</th>
                            <th class="table__th">Дата</th>
                            <th class="table__th">Сумма</th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($transactions as $transaction)
                        <tr class="table__tr mt-2 mb-2 "  >
                            <td class="table__td">{{ $transaction->id }}</td>
{{--                            <td class="table__td">{!! $user->car ? '<i class="icmn-truck text-primary"></i>': '' !!}</td>--}}
                            <td class="table__td">
                                {{ ($transaction->user->profile->surname ?? '').' '.($transaction->user->profile->name ?? '').' '.($transaction->user->profile->middle_name ?? '') }}
                            </td>
                            <td class="table__td">
                                @if($transaction->company_id)
                                    {{ $transaction->company->title ?? '' }}
                                @else
                                    Самозанятый
                                @endif
                            </td>
                            <td class="table__td">{{ $transaction->type == 'refill' ? 'Пополнение': 'Списание' }}</td>
                            <td class="table__td">{{ $transaction->description ?? 'Не указано' }}</td>
                            <td class="table__td">{{ $transaction->created_at->format('d.m.Y H:i') }}</td>
                            <td class="table__td">
                                {!! $transaction->type == 'refill'? '<span class="text-success">+'.number_format($transaction->amount, 0, '', ' ').'</span>': '<span class="text-danger">-'.number_format($transaction->amount, 0, '', ' ').'</span>' !!}
                            </td>
                            <td class="table__td" >
                                <div class="dropdown"  >
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin.transactions.show', $transaction) }}" class="dropdown-item">
                                            Просмотр
                                        </a>
                                        <div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.destroy_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.transactions.destroy', $transaction) }}" id="delete_form" class="d-inline-block" method="post">
                                                @csrf
                                                @method('delete')
                                                <span class="d-block text-danger"
                                                      >
                                                    @lang('admin.delete')
                                                </span>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @slot('bottom')
            @include('ui.pagination', ['data' => $transactions])
        @endslot
    @endcomponent
@endsection
