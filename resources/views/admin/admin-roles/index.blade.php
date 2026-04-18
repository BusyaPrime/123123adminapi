@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Роли пользователей', 'bodyClass' => 'card-body-no-padding'])

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

                <a href="{{ route('admin.admins.index') }}" class="btn btn-default ml-2">
                    <span class="d-none d-sm-inline-block">Администраторы</span>
                </a>

                <a href="{{ route('admin.admins.roles.create') }}" class="btn btn-info ml-2">
                    <span class="d-none d-sm-inline-block">Добавить</span>
                </a>
            </div>

        @endslot

        @if($roles->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                    <tr>
                        <th class="table__th">Название роли</th>
                        <th class="table__th"></th>
                    </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($roles as $role)
                        <tr class="table__tr mt-2 mb-2 "  >
                            <td class="table__td">{{ $role->title ?? '--' }}</td>
                            <td class="table__td" >
                                <div class="dropdown"  >
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin.admins.roles.edit', $role) }}" class="dropdown-item">
                                            @lang('admin.edit')
                                        </a>
                                        <div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.destroy_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.admins.roles.destroy', $role) }}" id="delete_form" class="d-inline-block" method="post">
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
            @include('ui.pagination', ['data' => $roles])
        @endslot
    @endcomponent
@endsection
