@extends('admin.layout')

@push('meta')
    <script>
        document.documentElement.classList.add('bookings-page-js');
    </script>
@endpush
@section('center_content')
    @component('component.card', ['title' => 'Заказы', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')
            <div class="row col-sm-6 justify-content-end">
                <a href="{{ route('admin.bookings.statistics-yearly', ['payment_type' => 'cash']) }}" class="btn btn-default ml-2">
                    <span class="d-none d-sm-inline-block">Годовой отчет (Наличные)</span> <i class="icmn-plus"><!-- --></i>
                </a>

                <a href="{{ route('admin.bookings.statistics-yearly', ['payment_type' => 'company']) }}" class="btn btn-default ml-2">
                    <span class="d-none d-sm-inline-block">Годовой отчет (Перечисление)</span> <i class="icmn-plus"><!-- --></i>
                </a>

                <a href="{{ route('admin.bookings.export', $filters) }}" class="btn btn-default ml-2">
                    <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
                </a>

                @if($filters['id'] != '' || $filters['driver_id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['date_start'] != '' || $filters['date_end'] != '' || $filters['name'] != '' || $filters['region_from_id'] != '' || $filters['region_to_id'] != '' || $filters['cargo_type_id'] != '' || $filters['status'] != '' || $filters['car_type'] != '')
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

                    <a href="{{ route('admin.bookings.create') }}" class="btn btn-info ml-2 px-3">
                        <span class="d-none d-sm-inline-block">Добавить</span> <i class="icmn-plus"><!-- --></i>
                    </a>
            </div>
        @endslot

        @slot('filters')
            <section class="content" id="filter_block" style="{{ $filters['id'] != '' || $filters['driver_id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['date_start'] != '' || $filters['date_end'] != '' || $filters['name'] != '' || $filters['region_from_id'] != '' || $filters['region_to_id'] != '' || $filters['cargo_type_id'] != '' || $filters['status'] != '' || $filters['client_company_id'] != '' || $filters['car_type'] != '' ?'':'display: none' }}">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="{{ route('admin.bookings.index') }}" id="filter_block_form">
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

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="car_type">
                                                        <option value="">Тип машины</option>
                                                        @foreach($carTypes as $carType)
                                                            <option value="{{ $carType->id }}" {{ $filters['car_type'] == $carType->id? 'selected':'' }}>{{ $carType->title }}</option>
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
                                                    <input type="text" name="company_id" value="{{ $filters['company_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Компании">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="driver_id" value="{{ $filters['driver_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Водителя">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="status">
                                                        <option value="">Статус</option>
                                                        @foreach(trans('admin.booking_statuses') as $key => $status)
                                                            @if($key != 'order' && $key != 'new')
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

                                            <div class="col-sm-3">
                                            <div class="form-group">
                                                <select class="form-control" name="client_company_id">
                                                    <option value="">Юридический клиент</option>
                                                    @foreach($companyClients as $company)
                                                        <option value="{{ $company->id }}" {{ $filters['client_company_id'] == $company->id? 'selected':'' }}>{{ $company->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


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
                                        </div>

                                        <div class="row align-items-center justify-content-end">
                                            <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                <a href="{{ route('admin.bookings.index') }}" class="btn btn-danger mr-2" style="{{ $filters['id'] != '' || $filters['driver_id'] != '' || $filters['user_id'] != '' || $filters['company_id'] != '' || $filters['date_start'] != '' || $filters['date_end'] != '' || $filters['name'] != '' || $filters['region_from_id'] != '' || $filters['region_to_id'] != '' || $filters['cargo_type_id'] != '' || $filters['status'] != '' || $filters['client_company_id'] != '' ?'':'display: none' }};">Сбросить фильтры</a>
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
            <div class="bookings-table-shell">
                <table class="table bookings-table">
                    <thead class="table__thead text-center">
                    <tr>
                        <th class="table__th">ID</th>
                        <th class="table__th">Пользователь</th>
                        <th class="table__th">Точка А/Точка Б</th>
                        <th class="table__th">Тип и тоннаж</th>
                        <th class="table__th">Статус обработки</th>
                        <th class="table__th">Статус заказа</th>
                        <th class="table__th">Доп. факторы</th>
                        <th class="table__th">Просмотрено @if(in_array('driver_price_offers', $user_permissions)) <p class="text-warning m-1">Предложения</p> @endif</th>
                        <th class="table__th bookings-table__date-col">Дата</th>
                        <th class="table__th">
                            <span>Стоимость</span>
                            <div>Тип оплаты</div>
                            <span>Комиссия</span>
                        </th>
                        <th class="table__th"></th>
                    </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($bookings as $booking)

                    @php
                        $pickupDate = new DateTime(($booking->pickup_date??'').' '.($booking->pickup_time??''));
                        $now = new DateTime('today');
                    @endphp
                        <tr class="table__tr mt-2 mb-2 redirect-show-page text-center"  data-url="{{ route('admin.bookings.show', $booking) }}">
                            <td class="table__td">{{ $booking->id }}</td>
                            <td class="table__td">
                                @if($booking->user)
                                    @if($booking->user != '' || $booking->user->name != '' || $booking->user->middle_name != '')
                                        {{ ($booking->user->surname ?? '').' '.($booking->user->name ?? '').' '.($booking->user->middle_name ?? '') }}
                                    @else
                                        @if($booking->user)
                                            +{{ $booking->user->user->username ?? '' }}
                                        @endif
                                    @endif
                                @else
                                    Удалённый аккаунт
                                @endif
                            </td>

                            <td class="table__td">
                                @php
                                    $routes = json_decode($booking->routes ?? '', true);
                                @endphp
                                @foreach ($routes as $index => $route)
                                    {{ isset($route['address']) ? (mb_strlen($route['address']) > 50 ? mb_substr($route['address'], 0, 50).'...' : $route['address']) : 'Адрес не указан' }}
                                    @if($index + 1 != count($routes))
                                        @if($booking->is_round_trip == 0)
                                            <span class="small"><i class="fas fa-arrow-right"></i></span>
                                        @else
                                            <span style="color:orange; font-size:16px"><i class="fas fa-arrows-alt-h"></i></span>
                                        @endif
                                    @endif
                                @endforeach
                                
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
                                    @if($booking->client_company_id != null)
                                        <i class="fas fa-building bookings-company-icon" aria-hidden="true"></i>
                                    @endif
                                    @if($booking->partial_percentage > 0)
                                        <span class="rounded-circle bg-warning ml-2" style="width: 28px;height:28px;color: #fff!important;">
                                            {{$booking->partial_percentage}}%
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="table__td">
                                <div class="hover_container">
                                    <span class="small"><i class="fas fa-eye"></i></span>&nbsp;&nbsp;
                                    <span class="d-inline-block">{{ $booking->views_count }}</span>
                                        <div class="cars-container bookings-preview-container"
                                             data-preview-url="{{ route('admin.bookings.views-preview', $booking) }}">
                                            <div class="bookings-preview-loading">Загрузка...</div>
                                    </div>
                                </div>

                                @if(in_array('driver_price_offers', $user_permissions))
                                    <div class="m-2"></div>
                                    <div class="hover_container">
                                    <span class="small"><i class="fas fa-money-bill"></i></span>&nbsp;&nbsp;
                                    <span class="d-inline-block">{{ $booking->booking_price_offer_count }}</span>
                                        <div class="cars-container bookings-preview-container"
                                             data-preview-url="{{ route('admin.bookings.price-offers-preview', $booking) }}">
                                            <div class="bookings-preview-loading">Загрузка...</div>
                                    </div>
                                </div>
                                @endif
                            </td>

                            <td class="table__td bookings-table__date-col">
                                {{ $booking->created_at ? $booking->created_at->format('d.m.Y'): '--'}}
                            </td>

                            <td class="table__td">
                                {{ number_format($booking->price, 0, '', ' ') ?? 0}} сум
                                <div class="text-info">{{ trans('admin.payment_types.'.$booking->payment_type) }}</div>
                                {{ number_format($booking->commission, 0, '', ' ') ?? 0}} сум
                            </td>
                            <td class="table__td tooltipContainer {{ ($pickupDate < $now && ($booking->status == \App\Domain\TruckBookings\Models\TruckBooking::STATUS_ACCEPTED || $booking->status == \App\Domain\TruckBookings\Models\TruckBooking::STATUS_ORDER)) ? 'danger-popup' : '' }}">
                                <i class="fas fa-info-circle"></i>
                                <div class="tooltipD">
                                    <h4 class="tooltipH">Информация</h4>
                                    <div class="tooltipContent">
                                        <p class="mb-0">
                                            Время погрузки : {{ $booking->pickup_date.' '.$booking->pickup_time }}
                                        </p>
                                    </div>
                                    @if(null !== $booking->recomended_price && $booking->recomended_price > 0)
                                        <div class="tooltipContent">
                                            <p>
                                                Рекомендованная цена : {{ number_format($booking->recomended_price, 0, '', ' ') }} сум
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @push('styles')
            <style>
                .bookings-table-shell {
                    transition: opacity .12s ease, transform .12s ease;
                }

                html.bookings-page-js .bookings-table-shell {
                    opacity: 0;
                    transform: translateY(8px);
                }

                html.bookings-page-ready .bookings-table-shell {
                    opacity: 1;
                    transform: none;
                }

                .hover_container{
                    position: relative;
                }

                .bookings-table {
                    width: 100%;
                }

                .bookings-table__date-col {
                    white-space: nowrap;
                    min-width: 95px;
                    font-variant-numeric: tabular-nums;
                }

                .bookings-company-icon {
                    font-size: 20px;
                    color: #0c5a85;
                }

                .cars-container{
                    position: absolute;
                    right: 10px;
                    top: 12px;
                    border-radius:10px;
                    box-shadow: 0 0 5px rgba(0,0,0,.3);
                    padding:4px;
                    background: #fefefe;
                    z-index: 999;
                    /* display:none; */
                    visibility:hidden;
                    opacity:0;
                    transition: .2s all;
                }

                .bookings-preview-loading {
                    padding: 8px 10px;
                    font-size: 12px;
                    color: #6c757d;
                    white-space: nowrap;
                }

                .car{
                    padding:3px 5px;
                    min-width:250px;
                    border-bottom:1px solid #efefef;
                    margin: 3px 0;
                }
                
                .car:last-child{
                    border:none;
                    margin-bottom:0;
                }

                .car-container{
                    display:flex;
                    align-items:center;
                }
                .car-container .avatar{
                    flex:1 0 30px;
                    width: 30px;
                    max-width: 30px;
                    border-radius: 50%;
                    overflow:hidden;
                }
                
                .car-container .avatar img{
                    width:100%;
                }
                
                .car-container .driver-name{
                    flex:1 0;
                    padding: 5px;
                    font-size:13px;
                    line-height:12px;
                }
                
                .car-container .time{
                    flex:0 0 80px;
                    width:80px;
                    padding: 0 10px;
                    font-size:11px;
                    line-height:11px;
                }

                .hover_container:hover .cars-container{
                    visibility: visible;
                    opacity: 1;
                }
                

            </style>
        @endpush

        @push('scripts')
            <script>
                (function () {
                    function markBookingsReady() {
                        window.requestAnimationFrame(function () {
                            window.requestAnimationFrame(function () {
                                document.documentElement.classList.add('bookings-page-ready');
                            });
                        });
                    }

                    function loadBookingsPreview($trigger) {
                        var $container = $trigger.find('.bookings-preview-container');
                        var previewUrl = $container.data('previewUrl');

                        if (!previewUrl || $container.data('loaded') || $container.data('loading')) {
                            return;
                        }

                        $container.data('loading', true);

                        $.get(previewUrl)
                            .done(function (html) {
                                $container.html(html);
                                $container.data('loaded', true);
                            })
                            .fail(function () {
                                $container.html('<div class="car"><div class="car-container"><div>Не удалось загрузить</div></div></div>');
                            })
                            .always(function () {
                                $container.data('loading', false);
                            });
                    }

                    if (document.readyState === 'complete') {
                        markBookingsReady();
                    } else {
                        window.addEventListener('load', markBookingsReady, { once: true });
                    }

                    $(function () {
                        $('.hover_container').on('mouseenter focusin', function () {
                            loadBookingsPreview($(this));
                        });

                        $('.hover_container, .cars-container').on('click', function (event) {
                            event.stopPropagation();
                        });
                    });
                })();
            </script>
        @endpush

        @slot('bottom')
            @include('ui.pagination', ['data' => $bookings])
        @endslot
        @endcomponent
@endsection
