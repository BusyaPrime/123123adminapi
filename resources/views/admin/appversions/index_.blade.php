@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Логистические Компании', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')

{{--            @component('component.modal', ['id' => 'city_filter', 'class' => 'btn btn-sm btn-secondary ml-2'])--}}
{{--                @slot('label')--}}
{{--                    <i class="icmn-filter" ></i>--}}
{{--                @endslot--}}
{{--                @slot('title')--}}
{{--                    @lang('admin.filters')--}}
{{--                @endslot--}}

{{--                <div id="filters">--}}
{{--                    <form action="{{ route('admin.companies.index') }}" method="get">--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="id">--}}
{{--                                        <small>@lang('admin.id')</small>--}}
{{--                                    </label>--}}
{{--                                    <input class="form-control input-sm" name="id" id="id" type="number" step="1" min="1" value="{{ $filters['id'] ?? '' }}"/>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="title">--}}
{{--                                        <small>@lang('admin.title')</small>--}}
{{--                                    </label>--}}
{{--                                    <input class="form-control input-sm" name="title" id="title" type="text"  value="{{ $filters['title'] ?? '' }}"/>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="contract_number">--}}
{{--                                        <small>@lang('admin.contract_number')</small>--}}
{{--                                    </label>--}}
{{--                                    <input class="form-control input-sm" name="contract_number" id="contract_number" type="text"  value="{{ $filters['contract_number'] ?? '' }}"/>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="col-md-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="sort">--}}
{{--                                        <small>@lang('admin.sort')</small>--}}
{{--                                    </label>--}}
{{--                                    <select name="sort" id="sort" class="form-control input-sm">--}}
{{--                                        <option value=""></option>--}}
{{--                                        <option value="id" {{ (isset($filters['sort']) && $filters['sort'] == 'id') ? 'selected': ''}}>@lang('admin.id') (@lang('admin.asc'))</option>--}}
{{--                                        <option value="-id" {{ (isset($filters['sort']) && $filters['sort'] == '-id') ? 'selected': ''}}>@lang('admin.id') (@lang('admin.desc'))</option>--}}

{{--                                        <option value="title" {{ (isset($filters['sort']) && $filters['sort'] == 'title') ? 'selected': ''}}>@lang('admin.title') (@lang('admin.asc'))</option>--}}
{{--                                        <option value="-title" {{ (isset($filters['sort']) && $filters['sort'] == '-title') ? 'selected': ''}}>@lang('admin.title') (@lang('admin.desc'))</option>--}}

{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="float-left">--}}
{{--                                    <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-danger">@lang('admin.filters_reset')</a>--}}
{{--                                    <button class="btn btn-sm btn-success">@lang('admin.filters_apply')</button>--}}
{{--                                </div>--}}
{{--                                <div class="clearfix"></div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            @endcomponent--}}

<div class="row col-sm-6 justify-content-end">

    <a href="{{ route('admin.companies.export', $filters) }}" class="btn btn-default ml-2">
        <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
    </a>
    @if(($filters['title'] != '' || $filters['created_at'] != '' || $filters['active'] != ''))
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
    <a href="{{ route('admin.companies.create') }}" class="btn btn-info ml-2 px-3">
        <span class="d-none d-sm-inline-block">Добавить компанию</span> <i class="icmn-plus"><!-- --></i>
    </a>
</div>
        @endslot
        @slot('filters')
            <section class="content" id="filter_block" style="{{ ($filters['title'] != '' || $filters['created_at'] != '' || $filters['active'] != '') ?'':'display: none' }};">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="{{ route('admin.companies.index') }}" id="filter_block_form">
                                <div class="border rounded p-3">
                                    <div class="form-group">
                                        <div class="row justify-content-between">

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <input type="text" name="title" value="{{ $filters['title'] }}" class="form-control" placeholder="Поиск по наименованию">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="active">
                                                        <option value="">Статус компании</option>
                                                        <option value="1" {{ $filters['active'] == 1? 'selected':'' }}>@lang('admin.active')</option>
                                                        <option value="0" {{ $filters['active'] == 0 && $filters['active'] != ''? 'selected':'' }}>@lang('admin.not_active')</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="sort">
                                                        <option value="-id" {{ $filters['sort'] == '-id'? 'selected':'' }}>Сначала новые</option>
                                                        <option value="id" {{ $filters['sort'] == 'id'? 'selected':'' }}>Сначала старые</option>
                                                        <option value="title" {{ $filters['sort'] == 'title'? 'selected':'' }}>По наименованию (A-Z)</option>
                                                        <option value="-title" {{ $filters['sort'] == '-title'? 'selected':'' }}>По наименованию (Z-A)</option>
                                                        <option value="cars" {{ $filters['sort'] == 'cars'? 'selected':'' }}>Меньше всего авто</option>
                                                        <option value="-cars" {{ $filters['sort'] == '-cars'? 'selected':'' }}>Больше всего авто</option>
                                                        <option value="-active" {{ $filters['sort'] == '-active'? 'selected':'' }}>Сначала активные</option>
                                                        <option value="active" {{ $filters['sort'] == 'active'? 'selected':'' }}>Сначала заблокированные</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row align-items-center justify-content-end">
                                            <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                <a href="{{ route('admin.companies.index') }}" class="btn btn-danger mr-2" style="{{ ($filters['title'] != '' || $filters['created_at'] != '' || $filters['active'] != '') ?'':'display: none' }};">Сбросить фильтры</a>
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

        @if($companies->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">@lang('admin.id')</th>
                            <th class="table__th">@lang('validation.attributes.title')</th>
                            <th class="table__th">Кол-во транспорта</th>
                            <th class="table__th">@lang('validation.attributes.phone')</th>
                            {{--<th>@lang('common.phones')</th>--}}
                            {{--<th>@lang('common.emails')</th>--}}
                            {{--<th>@lang('common.console_number')</th>--}}
                            <th class="table__th">Дата регистрации</th>
                            <th class="table__th">@lang('validation.attributes.active')</th>
                            <th class="table__th"></th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($companies as $company)
                        <tr class="table__tr mt-2 mb-2 "  >
                            <td class="table__td">{{ $company->id }}</td>
                            <td class="table__td">{{ $company->title }}</td>
                            <td class="table__td">
                                {{ $company->users_count ?? 0 }}
                            </td>
                            {{--<td>{!! nl2br($company->address) !!}</td>--}}
                            {{--<td>--}}
                                {{--@foreach($company->getParsedPhones() as $phone)--}}
                                    {{--{{ $phone }}--}}
                                    {{--@if(!$loop->last) <br> @endif--}}
                                {{--@endforeach--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--@foreach($company->getParsedEmails() as $email)--}}
                                    {{--{{ $email }}--}}
                                    {{--@if(!$loop->last) <br> @endif--}}
                                {{--@endforeach--}}
                            {{--</td>--}}
                            {{--<td>{{ $company->console_number }}</td>--}}
                            <td class="table__td">
                                @if($company->user && $company->user)
                                    {{ $company->user->username }}
                                @else
                                    Не указан
                                @endif
                            </td>
                            <td class="table__td">
                                {{ $company->created_at->format('d.m.Y') }}
                            </td>
                            <td class="table__td">
                                {!! $company->active? '<span class="badge badge-success">'.trans('admin.active').'</span>': '<span class="badge badge-danger">'.trans('admin.not_active').'</span>' !!}
                            </td>
                            <td class="table__td">
                                <div class="dropdown"  >
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin.companies.show', $company) }}" class="dropdown-item">
                                            Просмотр
                                        </a>
                                        <a href="{{ route('admin.companies.edit', $company) }}" class="dropdown-item">
                                            @lang('admin.edit')
                                        </a>
                                        <div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.destroy_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.companies.destroy', $company) }}" id="delete_form" class="d-inline-block" method="post">
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
            @include('ui.pagination', ['data' => $companies])
        @endslot
    @endcomponent
@endsection
