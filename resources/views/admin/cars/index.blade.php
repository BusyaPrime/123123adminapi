@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Водители', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')

            <div class="row col-sm-6 justify-content-end">

                @component('component.modal', ['id' => 'push_all', 'class' => 'btn btn-sm btn-success'])
                    @slot('label')
                        <i class="icmn-bubble"></i> Отправить пуш всем
                    @endslot
                    @slot('title')
                        Отправить пуш всем
                    @endslot
                    <div >
                        <form action="{{ route('admin.cars.send-push') }}" method="post">
                            @csrf
                            <div class="form-group row ">
                                <label class="col-md-3 text-md-right col-form-label-sm" for="title">Тема</label>
                                <div class="col-md-9">
                                    <input type="text" name="title" class="form-control input-sm" id="title" value="{{ old('title', '') }}" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-3 text-md-right col-form-label-sm" for="message">Сообщение</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" name="message" id="message" cols="30" rows="10" required>{{ old('message') }}</textarea>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-12 text-center">
                                    <button class="btn btn-success" type="submit">Отправить</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endcomponent

                    <a href="{{ route('admin.cars.export', $filters) }}" class="btn btn-default ml-2">
                        <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
                    </a>
                @if($filters['moderated'] != '' || $filters['company_id'] != '' || $filters['user_id'] != '' || $filters['name'] != '' || $filters['car_type_id'] != '' || $filters['active'] != '' || $filters['username'] != '')
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
                <a href="{{ route('admin.cars.create') }}" class="btn btn-info ml-2">
                    <span class="d-none d-sm-inline-block">@lang('admin.create')</span> <i class="icmn-plus"><!-- --></i>
                </a>
            </div>

        @endslot
        @slot('filters')
            <section class="content" id="filter_block" style="{{ ($filters['moderated'] != '' || $filters['company_id'] != '' || $filters['user_id'] != '' || $filters['name'] != '' || $filters['car_type_id'] != '' || $filters['active'] != '' || $filters['username'] != '') ?'':'display: none' }};">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="{{ route('admin.cars.index') }}" id="filter_block_form">
                                <div class="border rounded p-3">
                                    <div class="form-group">
                                        <div class="row justify-content-between">

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control" placeholder="По Ф.И.О">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="username" value="{{ $filters['username'] ?? '' }}" class="form-control" placeholder="По номеру телефона">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="user_id" value="{{ $filters['user_id'] ?? '' }}" class="form-control" placeholder="По ID">
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
                                                    <input type="text" name="company_id" value="{{ $filters['company_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID компании">
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
                                                    <select class="form-control" name="moderated">
                                                        <option value="">Модерация</option>
                                                        <option value="1" {{ $filters['moderated'] == 1? 'selected':'' }}>Прошли модерацию</option>
                                                        <option value="0" {{ $filters['moderated'] == 0 && $filters['moderated'] != ''? 'selected':'' }}>Ожидают модерацию</option>
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
                                                <a href="{{ route('admin.cars.index') }}" class="btn btn-danger mr-2" style="{{ ($filters['moderated'] != '' || $filters['company_id'] != '' || $filters['user_id'] != '' || $filters['name'] != '' || $filters['car_type_id'] != '' || $filters['active'] != '') ?'':'display: none' }};">Сбросить фильтры</a>
                                                <button onclick="$('#filter_block_form').submit();" class="btn btn-info">Применить</button>
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
                        {{--                            <th class="table__th"></th>--}}
                                                    <th class="table__th">Компания</th>
                                                    <th class="table__th">Тип транспорт</th>
                        <th class="table__th">Рейтинг</th>
                        <th class="table__th">Дата регистрации</th>
                        <th class="table__th">Заказов</th>
                        <th class="table__th">Заработано</th>
                        <th class="table__th"></th>
                        <th class="table__th">@lang('validation.attributes.active')</th>
                        <th class="table__th"></th>
                    </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($cars as $car)
                        <tr class="table__tr mt-2 mb-2 " >
                            <td class="table__td">{{ $car->user->id }}</td>
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
                                    {{ \App\Domain\TruckBookings\Models\TruckBooking::where('driver_id', $car->user->id)->count() ?? 0}}
                                @else
                                    0
                                @endif
                            </td>
                            <td class="table__td">
                                @if($car->user)
                                    <div><strong>Сумма:</strong> {{ number_format(\App\Domain\TruckBookings\Models\TruckBooking::where('driver_id', $car->user->id)->where('status', 'done')->sum('price'), 0, '', ' ') ?? 0}} сум</div>
                                    <div><strong>Отчислений:</strong> {{ number_format(\App\Domain\TruckBookings\Models\TruckBooking::where('driver_id', $car->user->id)->where('status', 'done')->sum('commission'), 0, '', ' ') ?? 0}} сум</div>
                                @else
                                    <div><strong>Сумма:</strong> 0 сум</div>
                                    <div><strong>Отчислений:</strong> 0 сум</div>
                                @endif
                            </td>
                            <td class="table__td">
                                @if(!$car->moderated)
                                    <span class="badge badge-warning text-warning rounded-circle p-0" style="vertical-align: middle;width: 16px; height: 16px;">.</span>
                                @endif
                            </td>
                            <td class="table__td">

                                    {!! $car->active? '<span class="badge badge-success" style="vertical-align: middle">'.trans('admin.active').'</span>': '<span class="badge badge-danger" style="vertical-align: middle">'.trans('admin.not_active').'</span>' !!}
                            </td>
                            <td class="table__td">
                                <div class="dropdown">
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin.cars.show', $car) }}" class="dropdown-item">
                                            Просмотр
                                        </a>
                                        @if($car->user)
                                            <a href="{{ route('admin.chat.index', ['chat_id' => $car->user->id, 'type' => 'drivers']) }}" class="dropdown-item">
                                                Чат
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.cars.edit', $car) }}" class="dropdown-item">
                                            @lang('admin.edit')
                                        </a>
                                        <div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.destroy_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.cars.destroy', $car) }}" id="delete_form" class="d-inline-block" method="post">
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
