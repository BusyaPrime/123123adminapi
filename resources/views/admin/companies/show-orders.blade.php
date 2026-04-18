@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Подробнее о компании', 'bodyClass' => 'card-body-no-padding'])

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
            <div class="col-sm-6 d-flex align-items-center justify-content-between">
                <a href="{{ route('admin.companies.index') }}" class="btn btn-info">Назад</a>
                <div class="balanse mb-0 mr-2 border rounded-pill px-3 py-2">
                    <span class="text-primary" style="font-weight: bold;">Баланс компании</span>
                    <span class="text-primary ml-4" style="font-size: 25px;">{{ $company->balance ?? 0 }} сум</span>
                </div>
            </div>

        @endslot

        <div class="row">
            <div class="col-sm-3">
                <div class="left-bar d-flex flex-column align-items-center border rounded-lg pt-3 pb-3" style="background-color: #002C47;">
                    <div class="px-5 pt-3 mb-2">
                        <img src="{{ asset('uploads/defaults/company.png') }}" class="img-fluid img-thumbnail rounded-circle" alt="">
                    </div>
                    <div class="text-white">
                        {{ $company->title ?? '--' }}
                    </div>
                    <p class="text-white">Логистическая компания</p>
                    <p class="text-white" style="font-size: 14px;">Количество заработанных средств</p>
                    <p class="text-green">0 сум
                        <a href="{{ route('admin.companies.show-orders', $company) }}" class="text-white ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </p>
                    <p class="text-white">
                        0
                    </p>
                </div>
                <div class="text-primary text-center mt-4" style="font-size: 25px;">
                    Заказы
                </div>

                <div class="border rounded-lg px-3 py-2">
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 20px;">Завершенные
                        <span class="text-green" style="font-size: 35px;">0</span>
                        <a href="{{ route('admin.companies.show-orders', $company) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>

                <div class="border rounded-lg mt-3  px-3 py-2">
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 20px;">Выполняемые
                        <span class="text-warning" style="font-size: 35px;">0</span>
                        <a href="{{ route('admin.companies.show-orders', $company) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>


                <div class="status text-center text-primary mt-3" style="font-size: 30px;">
                    Статус компании
                </div>
                <div class="status-btns text-center  border rounded-lg p-2">
                    {!! $company->active? '<span class="badge badge-success">'.trans('admin.active').'</span>': '<span class="badge badge-danger">'.trans('admin.not_active').'</span>' !!}
                </div>


                <div class="status text-center text-primary mt-3" style="font-size: 27px;">
                    Водители компании
                </div>

                <div class="border rounded-lg p-2 mt-4">
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 20px;">Количество
                        <span class="text-green" style="font-size: 35px;">0</span>
                        <a href="#" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>


            </div>


            <div class="col-sm-9">
                <div class="card">
                    <div class="card-header">
                        Заказы компании
                    </div>
                    <div class="card-body">

                        @if($orders->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table__thead">
                                    <tr>
                                        <th class="table__th">@lang('validation.attributes.id')</th>
                                        <th class="table__th">Точка А / Точка В</th>
                                        <th class="table__th">Тип / Тоннаж</th>
                                        <th class="table__th">Перевозчик</th>
                                        <th class="table__th">@lang('validation.attributes.active')</th>
                                        <th class="table__th">Стоимость</th>
                                        <th class="table__th"></th>
                                    </tr>
                                    </thead>
                                    <tbody class="table__tbody">
                                    @foreach($orders as $order)
                                        <tr class="table__tr mt-2 mb-2">
                                            <td class="table__td">{{ $order->id }}</td>
                                            <td class="table__td">Самарканд <i class="fas fa-arrow-right " style="font-size: 8px;color: #2b2b2b;vertical-align: baseline"></i> Ташкент</td>
                                            <td class="table__td">ТНВ / 18 тонн</td>
                                            <td class="table__td">Рожков В.Л</td>
                                            <td class="table__td">
                                                <span class="badge badge-success">Доставлен</span>
                                            </td>
                                            <td class="table__td">1 250 654 сум</td>
                                            <td class="table__td">
                                                <div class="dropdown">
                                                    <a href="#" class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        {{--                                                        <a href="{{ route('admin.users.edit', $user) }}" class="dropdown-item">--}}
                                                        {{--                                                            @lang('admin.edit')--}}
                                                        {{--                                                        </a>--}}
                                                        {{--                                                        <div class="dropdown-item " onclick="--}}
                                                        {{--                                                            if (confirm('@lang('admin.destroy_confirm')')) {--}}
                                                        {{--                                                            $('#delete_form').submit();--}}
                                                        {{--                                                            }--}}
                                                        {{--                                                            ">--}}
                                                        {{--                                                            <form action="{{ route('admin.users.destroy', $user) }}" id="delete_form" class="d-inline-block" method="post">--}}
                                                        {{--                                                                @csrf--}}
                                                        {{--                                                                @method('delete')--}}
                                                        {{--                                                                <span class="d-block text-danger"--}}
                                                        {{--                                                                >--}}
                                                        {{--                                                    @lang('admin.delete')--}}
                                                        {{--                                                </span>--}}
                                                        {{--                                                            </form>--}}
                                                        {{--                                                        </div>--}}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @include('ui.pagination', ['data' => $orders])
                    </div>

                </div>
            </div>
        </div>
    @endcomponent
@endsection
