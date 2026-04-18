@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Предл. и жалоб', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')

{{--            @component('component.modal', ['id' => 'filter', 'class' => 'btn btn-sm btn-secondary ml-2'])--}}
{{--                @slot('label')--}}
{{--                    <i class="icmn-filter"></i>--}}
{{--                @endslot--}}
{{--                @slot('title')--}}
{{--                    @lang('admin.filters')--}}
{{--                @endslot--}}

{{--                <div id="filters">--}}
{{--                    <form action="{{ route('admin.cargo-types.index') }}" method="get">--}}
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
{{--                                        <small>@lang('validation.attributes.title')</small>--}}
{{--                                    </label>--}}
{{--                                    <input class="form-control input-sm" name="title" id="title" type="text"  value="{{ $filters['title'] ?? '' }}"/>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-12">--}}
{{--                                <div class="float-left">--}}
{{--                                    <a href="{{ route('admin.cargo-types.index') }}" class="btn btn-sm btn-danger">@lang('admin.filters_reset')</a>--}}
{{--                                    <button class="btn btn-sm btn-success">@lang('admin.filters_apply')</button>--}}
{{--                                </div>--}}
{{--                                <div class="clearfix"></div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            @endcomponent--}}

{{--            <a href="{{ route('admin.cargo-types.create') }}" class="btn btn-sm btn-primary ml-2">--}}
{{--                <span class="d-none d-sm-inline-block">@lang('admin.create')</span> <i class="icmn-plus"><!-- --></i>--}}
{{--            </a>--}}

<div class="row col-sm-6 justify-content-end">
    <form action="{{ route('admin.tickets.index') }}" method="get" id="filter_form">

            <div class="form-group ">
                <select class="form-control js-presets" name="user_type">
                    <option value="" {{ ($filters['user_type'] == '') ? 'selected':'' }}>Все пользователи</option>
                    <option value="driver" {{ ($filters['user_type'] == 'driver') ? 'selected':'' }}>Водитель</option>
                    <option value="client" {{ ($filters['user_type'] == 'client') ? 'selected':'' }}>Пользователь</option>
                </select>
            </div>


    </form>
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
{{--    <a href="{{ route('admin.ticket-themes.create') }}" class="btn btn-info ml-2">--}}
{{--        <span class="d-none d-sm-inline-block">@lang('admin.create')</span> <i class="icmn-plus"><!-- --></i>--}}
{{--    </a>--}}
</div>

        @endslot

        @if($tickets->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">@lang('validation.attributes.id')</th>
                            <th class="table__th"></th>
                            <th class="table__th">Тема</th>
                            <th class="table__th">Статус</th>
                            <th class="table__th">Дата</th>
                            <th class="table__th"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($tickets as $item)
                        <tr class="table__tr mt-2 mb-2 ">
                            <td class="table__td">{{ $item->id }}</td>
                            <td class="table__td">
                                {{ $item->user_name }}
                                <div class="text-warning">
                                    {{ $item->user_type == 'driver' ? 'Водитель': 'Клиент' }}
                                </div>
                            </td>
                            <td class="table__td">{{ $item->subject ?? '--' }}</td>
                            <td class="table__td">{{ trans('admin.ticket_statuses.'.$item->status) }}</td>
                            <td class="table__td">{{ $item->created_at ? $item->created_at->format('d.m.Y H:i'): '--' }}</td>
                            <td class="table__td">
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin.tickets.edit', $item) }}" class="dropdown-item">
                                            @lang('admin.edit')
                                        </a>
                                        <div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.destroy_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.tickets.destroy', $item) }}" class="d-inline-block" method="post">
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
            @include('ui.pagination', ['data' => $tickets])
        @endslot
    @endcomponent
@endsection

@push('scripts')
    <script>
        $(function () {
            $('.js-presets').on('change', function () {
                $('#filter_form').submit();
            });
        });
    </script>
@endpush
