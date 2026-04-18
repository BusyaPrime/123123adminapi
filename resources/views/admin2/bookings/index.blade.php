@extends('admin2.layout')

@section('center_content')
    @component('component.card', ['title' => 'Заказы', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')
            <div class="row col-sm-6 justify-content-end">
				@if(isset($user) && $user->is_external ==1)
                    <a href="{{ route('admin2.bookings.export', $filters) }}" class="btn btn-default ml-2">
                        <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
                    </a>
				@endif
                @if($filters['driver_id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['date_start'] != '' || $filters['date_end'] != '' || $filters['name'] != '' || $filters['region_from_id'] != '' || $filters['region_to_id'] != '' || $filters['cargo_type_id'] != '' || $filters['status'] != '')
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
			@if(isset($user) && $user->is_external ==1)
				<section class="content" id="filter_block" style="{{ $filters['driver_id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['date_start'] != '' || $filters['date_end'] != '' || $filters['name'] != '' || $filters['region_from_id'] != '' || $filters['region_to_id'] != '' || $filters['cargo_type_id'] != '' || $filters['status'] != '' ?'':'display: none' }}">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="{{ route('admin2.bookings.index') }}" id="filter_block_form">
                                <div class="border rounded p-3">
                                    <div class="form-group">
                                        <div class="row justify-content-between">
										
											<div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="id" value="{{ $filters['id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Заказа">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control" placeholder="Поиск по Ф.И.О пользователя">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="user_id" value="{{ $filters['user_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Пользователя">
                                                </div>
                                            </div>


                                            {{--<div class="col-sm-3">--}}
											{{--<div class="form-group">--}}
												{{--<input type="text" name="driver_id" value="{{ $filters['driver_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Водителя">--}}
														{{--</div>--}}
												{{--</div>--}}

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="cargo_type_id">
                                                        <option value="">Тип груза</option>
                                                        @foreach($cargoTypes as $cargoType)
                                                            <option value="{{ $cargoType->id }}" {{ $filters['cargo_type_id'] == $cargoType->id? 'selected':'' }}>{{ $cargoType->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="region_from_id">
                                                        <option value="">Регион отправки</option>
                                                        @foreach($regions as $region)
                                                            <option value="{{ $region->id }}" {{ $filters['region_from_id'] == $region->id? 'selected':'' }}>{{ $region->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="region_to_id">
                                                        <option value="">Регион доставки</option>
                                                        @foreach($regions as $region)
                                                            <option value="{{ $region->id }}" {{ $filters['region_to_id'] == $region->id? 'selected':'' }}>{{ $region->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


{{--                                            <div class="col-sm-3">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <input type="text" name="company_id" value="{{ $filters['company_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Компании">--}}
{{--                                                </div>--}}
{{--                                            </div>--}}


                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="sort">
                                                        <option value="-id" {{ $filters['sort'] == '-id'? 'selected':'' }}>Сначала новые</option>
                                                        <option value="id" {{ $filters['sort'] == 'id'? 'selected':'' }}>Сначала старые</option>
                                                        <option value="-weight" {{ $filters['sort'] == '-weight'? 'selected':'' }}>Сначала самые тежелые</option>
                                                        <option value="weight" {{ $filters['sort'] == 'weight'? 'selected':'' }}>Сначала самые легкие</option>
                                                        <option value="-price" {{ $filters['sort'] == '-price'? 'selected':'' }}>Сначала самые дорогие</option>
                                                        <option value="price" {{ $filters['sort'] == 'price'? 'selected':'' }}>Сначала самые дешевые</option>
                                                    </select>
                                                </div>
                                            </div>
											
											<div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="status">
                                                        <option value="">Статус</option>
                                                        @foreach(trans('admin.booking_statuses') as $key => $status)
                                                            @if($key != 'order' && $key != 'new' && $key != 'free')
                                                                <option value="{{ $key }}" {{ $filters['status'] == $key? 'selected':'' }}>{{ $status }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

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
                                                <a href="{{ route('admin2.bookings.index') }}" class="btn btn-danger mr-2" style="{{ $filters['driver_id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['date_start'] != '' || $filters['date_end'] != '' || $filters['name'] != '' || $filters['region_from_id'] != '' || $filters['region_to_id'] != '' || $filters['cargo_type_id'] != '' || $filters['status'] != '' ?'':'display: none' }};">Сбросить фильтры</a>
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
			@else
            <section class="content" id="filter_block" style="{{ $filters['driver_id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['date_start'] != '' || $filters['date_end'] != '' || $filters['name'] != '' || $filters['region_from_id'] != '' || $filters['region_to_id'] != '' || $filters['cargo_type_id'] != '' || $filters['status'] != '' ?'':'display: none' }}">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="{{ route('admin2.bookings.index') }}" id="filter_block_form">
                                <div class="border rounded p-3">
                                    <div class="form-group">
                                        <div class="row justify-content-between">

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control" placeholder="Поиск по Ф.И.О пользователя">
                                                </div>
                                            </div>

{{--                                            <div class="col-sm-3">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <input type="text" name="user_id" value="{{ $filters['user_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Пользователя">--}}
{{--                                                </div>--}}
{{--                                            </div>--}}


                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="driver_id" value="{{ $filters['driver_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Водителя">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="cargo_type_id">
                                                        <option value="">Тип груза</option>
                                                        @foreach($cargoTypes as $cargoType)
                                                            <option value="{{ $cargoType->id }}" {{ $filters['cargo_type_id'] == $cargoType->id? 'selected':'' }}>{{ $cargoType->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="region_from_id">
                                                        <option value="">Регион отправки</option>
                                                        @foreach($regions as $region)
                                                            <option value="{{ $region->id }}" {{ $filters['region_from_id'] == $region->id? 'selected':'' }}>{{ $region->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="region_to_id">
                                                        <option value="">Регион доставки</option>
                                                        @foreach($regions as $region)
                                                            <option value="{{ $region->id }}" {{ $filters['region_to_id'] == $region->id? 'selected':'' }}>{{ $region->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


{{--                                            <div class="col-sm-3">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <input type="text" name="company_id" value="{{ $filters['company_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Компании">--}}
{{--                                                </div>--}}
{{--                                            </div>--}}


                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="sort">
                                                        <option value="-id" {{ $filters['sort'] == '-id'? 'selected':'' }}>Сначала новые</option>
                                                        <option value="id" {{ $filters['sort'] == 'id'? 'selected':'' }}>Сначала старые</option>
                                                        <option value="-weight" {{ $filters['sort'] == '-weight'? 'selected':'' }}>Сначала самые тежелые</option>
                                                        <option value="weight" {{ $filters['sort'] == 'weight'? 'selected':'' }}>Сначала самые легкие</option>
                                                        <option value="-price" {{ $filters['sort'] == '-price'? 'selected':'' }}>Сначала самые дорогие</option>
                                                        <option value="price" {{ $filters['sort'] == 'price'? 'selected':'' }}>Сначала самые дешевые</option>
                                                    </select>
                                                </div>
                                            </div>

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
                                                    <select class="form-control" name="status">
                                                        <option value="">Статус</option>
                                                        @foreach(trans('admin.booking_statuses') as $key => $status)
                                                            @if($key != 'order' && $key != 'new' && $key != 'free')
                                                                <option value="{{ $key }}" {{ $filters['status'] == $key? 'selected':'' }}>{{ $status }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row align-items-center justify-content-end">
                                            <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                <a href="{{ route('admin2.bookings.index') }}" class="btn btn-danger mr-2" style="{{ $filters['driver_id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['date_start'] != '' || $filters['date_end'] != '' || $filters['name'] != '' || $filters['region_from_id'] != '' || $filters['region_to_id'] != '' || $filters['cargo_type_id'] != '' || $filters['status'] != '' ?'':'display: none' }};">Сбросить фильтры</a>
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
			@endif
        @endslot
<style>
.icon_wrapper {
    width: 100%;
    float: left;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon_wrapper span.rounded-circle {
    display: flex !important;
    align-items: center;
    justify-content: center;
    font-size: 13px !important;
}
</style>
        @if($bookings->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                    <tr>
                        <th class="table__th">ID</th>
                        <th class="table__th">Пользователь</th>
                        <th class="table__th">Точка А/Точка Б</th>
                        <th class="table__th">Тип и тоннаж</th>
                        <th class="table__th">Статус обработки</th>
                        <th class="table__th">Статус заказа</th>
                        <th class="table__th">Дополнительно</th>
                        <th class="table__th">Дата</th>
                        <th class="table__th">Стоимость <div class="text-info">@if(isset($user) && $user->is_external ==1) Тип оплаты @else Комиссия @endif</div></th>
                        {{--                        <th></th>--}}
                    </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($bookings as $booking)
					<tr class="table__tr mt-2 mb-2 redirect-show-page"  data-url="{{ route('admin2.bookings.show', $booking) }}">
                        <!-- <tr class="table__tr mt-2 mb-2"> -->
                            <td class="table__td">{{ $booking->id }}</td>
                            <td class="table__td">
                                @if($booking->user && $booking->user->user != $company->user)
                                    {{ ($booking->user->surname ?? '').' '.($booking->user->name ?? '').' '.($booking->user->middle_name ?? '') }}
                                    @elseif(!$booking->user) Удалённый аккаунт
                                    @else {{ $company->title }}
                                @endif
                            </td>

                            <td class="table__td">
                                @php
                                    $routes = json_decode($booking->routes ?? '', true);
                                        $regionFrom = isset($routes[0]) && isset($routes[0]['address']) ? $routes[0]['address'] : 'Адрес не указан';
                                        $regionTo = isset($routes[1]) && isset($routes[1]['address']) ? $routes[1]['address'] : 'Адрес не указан';
                                @endphp
                                {{ $regionFrom ?? 'Регион не определен' }} @if($booking->is_round_trip == 0) <span class="small"><i class="fas fa-arrow-right"></i></span> @else <span style="color:orange; font-size:16px"><i class="fas fa-arrows-alt-h"></i></span> @endif {{ $regionTo ?? 'Регион не определен' }}
                            </td>
                            <td class="table__td">
                                {{ $booking->carType->translations[0]->title }} / {{ round(($booking->weight ?? 0) / 1000, 3) }} т.

                            </td>
                            <td class="table__td ">

                                {!! $booking->driver_id ? (($booking->driver->surname ?? '').' '.($booking->driver->name ?? '').' '.($booking->driver->middle_name ?? '')) : '<span class="btn btn-outline-primary active">Свободен</span>' !!}

                            </td>

                            <td class="table__td">
                                <span class="btn active btn-outline-{{trans('admin.booking_statuses_colors.'.$booking->status)}}">{{ trans('admin.booking_statuses.'.$booking->status) }}</span>
                            </td>
							
							<td class="table__td">
                            <div class="icon_wrapper">
							@if($booking->partial_percentage > 0)
							<span class="rounded-circle bg-warning ml-2" style="width: 28px;height:28px;color: #fff!important;">
								{{$booking->partial_percentage}}%
							</span>
							@endif
							</div>
                            </td>

                            <td class="table__td">
                                {{ $booking->clientCompany->title ?? '' }}
                            </td>

                            <td class="table__td">
                                {{ $booking->created_at ? $booking->created_at->format('d.m.Y'): '--'}}
                            </td>

                            <td class="table__td">
                                {{ $booking->price ?? 0}} сум
                                <div class="text-info">@if(isset($user) && $user->is_external == 1) {{ trans('admin.payment_types.'.$booking->payment_type) ?? '-'}} @else {{ $booking->commission ?? 0}} сум @endif</div>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @slot('bottom')
            @include('ui.pagination', ['data' => $bookings])
        @endslot
    @endcomponent
@endsection
