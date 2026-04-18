@extends('admin2.layout')

@section('center_content')
    @component('component.card', ['title' => 'Водители', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')

            <div class="row col-sm-6 justify-content-end">

                @if($filters['company_id'] != '' || $filters['name'] != '' || $filters['car_type_id'] != '' || $filters['active'] != '')
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
                <a href="{{ route('admin2.cars.create') }}" class="btn btn-info ml-2">
                    <span class="d-none d-sm-inline-block">@lang('admin.create')</span> <i class="icmn-plus"><!-- --></i>
                </a>
            </div>

        @endslot
        @slot('filters')
            <section class="content" id="filter_block" style="{{ ($filters['company_id'] != '' || $filters['name'] != '' || $filters['car_type_id'] != '' || $filters['active'] != '') ?'':'display: none' }};">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="{{ route('admin2.cars.index') }}" id="filter_block_form">
                                <div class="border rounded p-3">
                                    <div class="form-group">
                                        <div class="row justify-content-between">

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control" placeholder="Поиск по Ф.И.О">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select name="car_type_id" id="car_type_id" class="form-control">
                                                        <option value="">Все типы транспорта</option>
                                                        @foreach($car_types as $car_type)
                                                            <option value="{{ $car_type->id }}" {{ $filters['car_type_id'] == $car_type->id? 'selected':'' }}>{{ $car_type->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="active">
                                                        <option value="">Статус</option>
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
                                                        <option value="-rating" {{ $filters['sort'] == '-rating'? 'selected':'' }}>Сначала лушие по рейтингу</option>
                                                        <option value="rating" {{ $filters['sort'] == 'rating'? 'selected':'' }}>Сначала худшие по рейтингу</option>
                                                        <option value="-orders_count" {{ $filters['sort'] == '-orders_count'? 'selected':'' }}>Сначала больше всего заказов</option>
                                                        <option value="orders_count" {{ $filters['sort'] == 'orders_count'? 'selected':'' }}>Сначала меньше всего заказов</option>
                                                        <option value="-orders_commission" {{ $filters['sort'] == '-orders_commission'? 'selected':'' }}>Сначала больше всего отчислений</option>
                                                        <option value="orders_commission" {{ $filters['sort'] == 'orders_commission'? 'selected':'' }}>Сначала меньше всего отчислений</option>
                                                        <option value="-active" {{ $filters['sort'] == '-active'? 'selected':'' }}>Сначала активные</option>
                                                        <option value="active" {{ $filters['sort'] == 'active'? 'selected':'' }}>Сначала заблокированные</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row align-items-center justify-content-end">
                                            <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                <a href="{{ route('admin2.cars.index') }}" class="btn btn-danger mr-2" style="{{ ($filters['company_id'] != '' || $filters['name'] != '' || $filters['car_type_id'] != '' || $filters['active'] != '') ?'':'display: none' }};">Сбросить фильтры</a>
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

        @if($cars->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                    <tr>
                        <th class="table__th">@lang('validation.attributes.id')</th>
                        <th class="table__th">@lang('validation.attributes.name')</th>
                                                    <th class="table__th">Компания</th>
                                                    <th class="table__th">Тип транспорт</th>
                        <th class="table__th">Рейтинг</th>
                        <th class="table__th">Дата регистрации</th>
                        <th class="table__th">Заказов</th>
                        <th class="table__th">Заработано</th>
                        <th class="table__th">@lang('validation.attributes.active')</th>
                        <th class="table__th"></th>
                    </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($cars as $car)
                        <tr class="table__tr mt-2 mb-2 " >
                            <td class="table__td">{{ $car->user->id ?? $car->id }}</td>
                            <td class="table__td">{{ ($car->user->profile->surname ?? '').' '.($car->user->profile->name ?? '').' '.($car->user->profile->middle_name ?? '') }}</td>
                            <td class="table__td">
                                @if($car->user && $car->user->profile && $car->user->profile->company)
                                    {{ $car->user->profile->company->title }}
                                @else
                                    Cамозанятый
                                @endif
                            </td>
                            <td>
                                @if($car->carType)
                                    {{ $car->carType->title }}
                                @else
                                    Не узакан
                                @endif
                            </td>
                            <td class="table__td">
                                @if($car->user && $car->user->profile)
                                    {{ $car->user->profile->rating ?? 'Нет оценки' }}
                                @else
                                    Нет оценки
                                @endif
                            </td>
                            <td class="table__td">
                                {{ $car->created_at->format('d.m.Y') }}
                            </td>
                            <td class="table__td">
                                @if($car->user)
                                    {{ $car->orders_count ?? 0 }}
                                @else
                                    0
                                @endif
                            </td>
                            <td class="table__td">
                                @if($car->user)
                                    <div><strong>Сумма:</strong> {{ $car->done_price_sum ?? 0 }} сум</div>
                                    <div><strong>Отчислений:</strong> {{ $car->done_commission_sum ?? 0 }} сум</div>
                                @else
                                    <div><strong>Сумма:</strong> 0 сум</div>
                                    <div><strong>Отчислений:</strong> 0 сум</div>
                                @endif
                            </td>
                            <td class="table__td">
                                {!! $car->active? '<span class="badge badge-success">'.trans('admin.active').'</span>': '<span class="badge badge-danger">'.trans('admin.not_active').'</span>' !!}
                            </td>
                            <td class="table__td">
                                <div class="dropdown"  >
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin2.cars.show', $car) }}" class="dropdown-item">
                                            Просмотр
                                        </a>
{{--                                        @if($car->user)--}}
{{--                                            <a href="{{ route('admin2.chat.index', ['chat_id' => $car->user->id, 'type' => 'drivers']) }}" class="dropdown-item">--}}
{{--                                                Чат--}}
{{--                                            </a>--}}
{{--                                        @endif--}}
                                        <a href="{{ route('admin2.cars.edit', $car) }}" class="dropdown-item">
                                            @lang('admin.edit')
                                        </a>
                                        <div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.destroy_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin2.cars.destroy', $car) }}" id="delete_form" class="d-inline-block" method="post">
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
            @include('ui.pagination', ['data' => $cars])
        @endslot
    @endcomponent
@endsection
