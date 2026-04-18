@extends('admin2.layout')

@section('center_content')
    @component('component.card', ['title' => 'Транзакции', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')

<div class="row col-sm-6 justify-content-end">
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
                            <form method="get" action="{{ route('admin2.transactions.index') }}" id="filter_block_form">
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
                                                    <select class="form-control" name="type">
                                                        <option value="">Все типы транзакции</option>
                                                        <option value="refill" {{ $filters['type'] == 'refill'? 'selected':'' }}>Пополнения</option>
                                                        <option value="debt" {{ $filters['type'] == 'debt'? 'selected':'' }}>Списания</option>
                                                    </select>
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


                                        </div>
                                        <div class="row align-items-center justify-content-end">
                                            <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                <a href="{{ route('admin2.transactions.index') }}" class="btn btn-danger mr-2" style="{{ $filters['id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['type'] != '' || $filters['date_start'] != ''|| $filters['date_end'] != '' ?'':'display: none' }};">Сбросить фильтры</a>
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
                                {!! $transaction->type == 'refill'? '<span class="text-success">+'.$transaction->amount.'</span>': '<span class="text-danger">-'.$transaction->amount.'</span>' !!}
                            </td>
                            <td class="table__td" >
                                <div class="dropdown"  >
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin2.transactions.show', $transaction) }}" class="dropdown-item">
                                            Просмотр
                                        </a>
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
