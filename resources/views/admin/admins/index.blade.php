@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Администраторы', 'bodyClass' => 'card-body-no-padding'])

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
{{--                                    <input class="form-control input-sm" name="name" id="name" type="text"  value="{{ $filters['admin_name'] ?? '' }}"/>--}}
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
    @if(($filters['active'] != '' || $filters['username'] != '' || $filters['admin_name'] != '' || $filters['admin_role_id'] != ''))
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

    <a href="{{ route('admin.admins.roles.index') }}" class="btn btn-default ml-2">
        <span class="d-none d-sm-inline-block">Роли</span>
    </a>

    <a href="{{ route('admin.admins.create') }}" class="btn btn-info ml-2">
        <span class="d-none d-sm-inline-block">Добавить</span>
    </a>
</div>

        @endslot
        @slot('filters')
            <section class="content" id="filter_block" style="{{ ($filters['active'] != '' || $filters['username'] != '' || $filters['admin_name'] != '' || $filters['admin_role_id'] != '') ?'':'display: none' }}">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="{{ route('admin.admins.index') }}" id="filter_block_form">
                                <div class="border rounded p-3">
                                    <div class="form-group">
                                        <div class="row justify-content-between">


                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="admin_name" value="{{ $filters['admin_name'] }}" class="form-control" placeholder="Поиск по Ф.И.О">
                                                </div>
                                            </div>


                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="username" value="{{ $filters['username'] }}" class="form-control" placeholder="Поиск по логину">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="admin_role_id">
                                                        <option value="">Все роли</option>
                                                        @foreach(\App\Domain\AdminRoles\Models\AdminRole::all() as $role)
                                                            <option value="{{ $role->id }}" {{ $filters['admin_role_id'] == $role->id? 'selected':'' }}>{{ $role->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="active">
                                                        <option value="">Статус пользователя</option>
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
                                                        <option value="-active" {{ $filters['sort'] == '-active'? 'selected':'' }}>Сначала активные</option>
                                                        <option value="active" {{ $filters['sort'] == 'active'? 'selected':'' }}>Сначала заблокированные</option>
                                                    </select>
                                                </div>
                                            </div>


                                        </div>

                                        <div class="row align-items-center justify-content-end">
                                            <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                <a href="{{ route('admin.admins.index') }}" class="btn btn-danger mr-2" style="{{ ($filters['active'] != '' || $filters['username'] != '' || $filters['admin_name'] != '' || $filters['admin_role_id'] != '') ?'':'display: none' }};">Сбросить фильтры</a>
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

        @if($users->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">@lang('validation.attributes.id')</th>
                            <th class="table__th">@lang('validation.attributes.name')</th>
{{--                            <th class="table__th"></th>--}}
                            <th class="table__th">Роль</th>
                            <th class="table__th">Логин</th>
                            <th class="table__th">Дата регистрации</th>
                            <th class="table__th">@lang('validation.attributes.active')</th>
                            <th class="table__th"></th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($users as $user)
                        <tr class="table__tr mt-2 mb-2 "  >
                            <td class="table__td">{{ $user->id }}</td>
{{--                            <td class="table__td">{!! $user->car ? '<i class="icmn-truck text-primary"></i>': '' !!}</td>--}}
                            <td class="table__td">{{ $user->name ?? '' }}</td>
                            <td class="table__td">{{ $user->adminRole->title ?? 'Не назначен' }}</td>
                            <td class="table__td">{{ $user->username }}</td>
                            <td class="table__td">{{ $user->created_at->format('m.d.Y') }}</td>
                            <td class="table__td">
                                {!! $user->active? '<span class="badge badge-success">'.trans('admin.active').'</span>': '<span class="badge badge-danger">'.trans('admin.not_active').'</span>' !!}
                            </td>
                            <td class="table__td" >
                                <div class="dropdown"  >
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin.admins.edit', $user) }}" class="dropdown-item">
                                            @lang('admin.edit')
                                        </a>
                                        <div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.destroy_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.admins.destroy', $user) }}" id="delete_form" class="d-inline-block" method="post">
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
            @include('ui.pagination', ['data' => $users])
        @endslot
    @endcomponent
@endsection
