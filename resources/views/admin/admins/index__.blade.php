@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => trans('admin.nav.admins'), 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')

{{--            @component('component.modal', ['id' => 'filter', 'class' => 'btn btn-sm btn-secondary ml-2'])--}}
{{--                @slot('label')--}}
{{--                    <i class="icmn-filter"></i>--}}
{{--                @endslot--}}
{{--                @slot('title')--}}
{{--                    @lang('admin.filters')--}}
{{--                @endslot--}}

{{--                <div id="filters">--}}
{{--                    <form action="{{ route('admin.admins.index') }}" method="get">--}}
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
{{--                                    <label for="name">--}}
{{--                                        <small>@lang('validation.attributes.name')</small>--}}
{{--                                    </label>--}}
{{--                                    <input class="form-control input-sm" name="name" id="name" type="text"  value="{{ $filters['name'] ?? '' }}"/>--}}
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
{{--                                    <a href="{{ route('admin.admins.index') }}" class="btn btn-sm btn-danger">@lang('admin.filters_reset')</a>--}}
{{--                                    <button class="btn btn-sm btn-success">@lang('admin.filters_apply')</button>--}}
{{--                                </div>--}}
{{--                                <div class="clearfix"></div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            @endcomponent--}}

            <a href="{{ route('admin.admins.create') }}" class="btn btn-sm btn-primary ml-2">
                <span class="d-none d-sm-inline-block">@lang('admin.create')</span> <i class="icmn-plus"><!-- --></i>
            </a>
        @endslot

        @if($users->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-default">
                        <tr>
                            <th>@lang('validation.attributes.id')</th>
                            <th>@lang('validation.attributes.name')</th>
                            <th>@lang('validation.attributes.username')</th>
                            <th>@lang('validation.attributes.active')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>
                                {!! $user->active? '<span class="badge badge-success">'.trans('admin.active').'</span>': '<span class="badge badge-danger">'.trans('admin.not_active').'</span>' !!}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin.admins.edit', $user) }}" class="dropdown-item">
                                            @lang('admin.edit')
                                        </a>
                                        <div class="dropdown-item ">
                                            <form action="{{ route('admin.admins.destroy', $user) }}" class="d-inline-block" method="post">
                                                @csrf
                                                @method('delete')
                                                <span class="d-block text-danger"
                                                      onclick="return confirm('@lang('admin.destroy_confirm')');">
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
