@extends('admin2.layout')

@section('center_content')
    @component('component.card', ['title' => 'Подробнее о заказах', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')
        @endslot
        <h5>Заказ</h5>
        <form class="border rounded-lg p-3 mb-3">
            <div class="form-group">
                <span class="text-primary">
                    Информация
                </span>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" style="background: transparent" placeholder="ID заказа: {{ $booking->id }}" disabled>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Пользователь: {{ ($booking->user->surname ?? '').' '.($booking->user->name ?? '').' '.($booking->user->middle_name ?? '') }}" disabled style="background: transparent">
                        <span class="input-group-text">
                            <a href="{{ route('admin2.bookings.show-user', $booking) }}" class="">
                                <i class="fas fa-eye"></i>
                            </a>
                        </span>
                    </div>
                </div>
                @php
                    $routes = json_decode($booking->routes ?? '', true);
                        $regionFrom = isset($routes[0]) && isset($routes[0]['address']) ? $routes[0]['address'] : 'Адрес не указан';
                        $regionTo = isset($routes[1]) && isset($routes[1]['address']) ? $routes[1]['address'] : 'Адрес не указан';
                @endphp
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Точка отправки заказа: {{ $regionFrom ?? 'Регион не определен' }}" disabled style="background: transparent">
                    </div>
                </div>
                
                @if($booking->client_company_id)
                    <div class="col-sm-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Компания: {{ $booking->clientCompany->title ?? '' }}" disabled style="background: transparent">
                        </div>
                    </div>
                @endif


                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Точка доставки заказа: {{ $regionTo ?? 'Регион не определен' }}" disabled style="background: transparent">
                    </div>
                </div>




                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Тип груза: {{ $booking->cargoType->title ?? 'Тип не указан' }}" disabled style="background: transparent">
                    </div>
                </div>




                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Тоннаж груза: {{ round(($booking->weight ?? 0) / 1000, 3) }} т." disabled style="background: transparent">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="ДШВ заказа: {{ ($booking->dimension_x ?? 0).'x'.($booking->dimension_y ?? 0).'x'.($booking->dimension_z ?? 0) }} м." disabled style="background: transparent">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Дистанция: {{ $booking->distance ?? 0}} км" disabled style="background: transparent">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Кто оплачивает: {{ $booking->payer == 'sender' ? 'Отправитель': ($booking->payer == 'receiver' ? 'Получатель' : 'Не указано') }}" disabled style="background: transparent">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Тип оплаты: {{ trans('admin.payment_types.'.$booking->payment_type) }}" disabled style="background: transparent">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Стоимость: {{ number_format($booking->price, 0, '', ' ') }} сум" disabled style="background: transparent">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Доп. оплата: {{ number_format($booking->additional_price, 0, '', ' ') }} сум" disabled style="background: transparent">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <textarea type="text" class="form-control" placeholder="Доп. оплата, комментарий: {{ $booking->additional_price_review ?? 'Нет' }}" disabled style="background: transparent"></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Комиссия агрегатора: {{ number_format($booking->commission, 0, '', ' ') ?? 0}} сум" disabled style="background: transparent">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Статус обработки{{ $booking->driver_id ? (': '.($booking->driver->surname ?? '').' '.($booking->driver->name ?? '').' '.($booking->driver->middle_name ?? '')) : '' }}" disabled style="background: transparent">
                        <span class="input-group-text {{$booking->driver_id ? '':'p-0'}}">
                            @if($booking->driver_id)
                                @if(isset ($booking->driver->user) && isset($booking->driver->user->car))
                                    <a href="{{ route('admin2.cars.show', $booking->driver->user->car->id ?? 0) }}" class="">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @else
                                    <span class="px-2">Удален</span>
                                @endif
                            @else
                                <span class="btn btn-outline-primary  rounded-0 active">Свободен</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Статус заказа " disabled style="background: transparent">
                        <span class="input-group-text p-0">
                            <span class="btn active rounded-0 btn-outline-{{trans('admin.booking_statuses_colors.'.$booking->status)}}">{{ trans('admin.booking_statuses.'.$booking->status) }}</span>
                        </span>
                    </div>
                </div>


                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Дата оформления заказа: {{ $booking->created_at ? $booking->created_at->format('d.m.Y H:i'): '--'}}" disabled style="background: transparent">
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Заказ назначен на: {{ $booking->pickup_date ? date('d.m.Y H:i', strtotime($booking->pickup_date.' '.$booking->pickup_time)): '--'}}" disabled style="background: transparent">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <div type="text" class="form-control rounded-0" style="background: transparent">Комментарий к заказу: {{ $booking->comment ?? 'Нет'}}</div>
                    </div>
                </div>
            </div>
            <div class="add-company__2 mt-3 d-flex align-items-center justify-content-between">
                <a href="{{ route('admin2.bookings.index') }}" class="btn btn-warning ">Назад</a>


                @if($booking->status != 'canceled' && $booking->status != 'done')
                <a class="btn btn-success" href="#" data-toggle="modal" data-target="#CompanyModal">Назначить водителя</a>
                @endif

                <a class="btnStyle" style="background: #75a99c; color: #fff; padding: 6px 15px; border-radius:20px;" data-toggle="modal" data-target="#additionalChargeModal">Доп. оплата</a>

                <a href="{{ route('admin2.chat.index-bookings', ['chat_id' => $booking]) }}" class="btn btn-primary ">Перейти в чат</a>

            </div>
        </form>

        <div id="map"></div>
    @endcomponent

    <div class="modal fade" id="CompanyModal"  aria-labelledby="CompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CompanyModalLabel">Назначить водителя</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin2.bookings.change-state', $booking) }}" method="post">
                        <input type="hidden" name="status" value="accepted">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="driver_id">Выберите водителя</label>
                                    <select name="driver_id" id="driver_id" class="form-control js-select d-block" required>
                                        @foreach($drivers as $driver)
                                            @if($driver->user)
                                                <option value="{{$driver->user->id}}">
                                                    {{$driver->user->id}} - {{ ($driver->user->profile->surname ?? '').' '.($driver->user->profile->name ?? '').' '.($driver->user->profile->middle_name ?? '') }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button class="btn btn-primary">Назначить</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="additionalChargeModal"  aria-labelledby="additionalChargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="additionalChargeModalLabel">Доп. оплата</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin2.bookings.addPrice', $booking) }}" method="post">
                        <div class="row">
                            <div class="col-10">
                                <div class="form-group">
                                    <label for="additionaL_price">Введите сумму</label>
                                    <input class="form-control price-input" type="text" maxLength='9' name="additional_price" id="additionaL_price" value="{{ $booking->additional_price ?? 0 }}" required>
                                </div>
                            </div>
							<div class="col-2">
								<div style='padding: 40px 5px'>
									сум
								</div>
							</div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="additional_price_review">Комментарий</label>
                                    <textarea class="form-control" name="additional_price_review" id="additional_price_review" maxlength="60" rows="3" required>{{ $booking->additional_price_review ?? '' }}</textarea>
									<span class="small">Макс. кол-во: 60 символов</span>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-warning">Изменить сумму</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        #map {
            width: 100%;
            height: 80vh;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://api-maps.yandex.ru/2.1/?apikey=858338d6-1dfc-4820-8d60-d6582151839e&lang=ru_RU" type="text/javascript">
    </script>
    <script>
        $(function () {
            ymaps.ready(init);

            function init() {
                var myMap = new ymaps.Map("map", {
                    center: [41.326681, 69.244031],
                    zoom: 11
                }, {
                    searchControlProvider: 'yandex#search'
                });
                @php
                    $routes = json_decode($booking->routes, true);
                @endphp

                @if(isset($routes[0]['lat']) && isset($routes[0]['lng']) && isset($routes[1]['lat']) && isset($routes[1]['lng']))
                var multiRoute = new ymaps.multiRouter.MultiRoute({
                    // Описание опорных точек мультимаршрута.
                    referencePoints: [
                        [{{ $routes[0]['lat'] }}, {{$routes[0]['lng']}}],
                        [{{ $routes[1]['lat'] }}, {{$routes[1]['lng']}}]
                    ],
                    // Параметры маршрутизации.
                    params: {
                        // Ограничение на максимальное количество маршрутов, возвращаемое маршрутизатором.
                        results: 1
                    }
                }, {
                    // Автоматически устанавливать границы карты так, чтобы маршрут был виден целиком.
                    boundsAutoApply: true
                });
                myMap.geoObjects.add(multiRoute);
                @endif

                @if($booking->driver_id && isset ($booking->driver->user) && isset($booking->driver->lat) && isset($booking->driver->lng))
                myMap.geoObjects.add(
                    new ymaps.Placemark([{{$booking->driver->lat}}, {{$booking->driver->lng}}], {
                        hintContent: '{{$booking->driver->user->car->number ?? '--'}}',
                        balloonContent: '{{$booking->driver->user->car->number ?? '--'}} <br>{{ (($booking->driver->surname ?? '').' '.($booking->driver->name ?? '').' '.($booking->driver->middle_name ?? '')) }} <br>+{{$booking->driver->user->username ?? '--'}}'
                    }, {
                        iconLayout: 'default#image',
                        iconImageSize: [20, 20],
                        iconImageHref: '{{ asset('images/green-circle.png') }}'
                    })
                );
                @endif

                @if(false)

                @endif
            }
        });
    </script>
@endpush

@push('scripts')
    	<script type="text/javascript" src="{{ asset('vendor/inputmask/jquery.inputmask.bundle.js') }}"></script>
    	<script>
            $(function () {
                $('.price-input').inputmask({
                    alias: 'numeric',
                    placeholder: '0',
                    groupSeparator: ' ',
                    autoGroup: true,
                    digits: 2,
                    digitsOptional: true

			});
		});
    </script>

@endpush

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            display: block;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {

            $('#CompanyModal').on('show.bs.modal', function (event) {
                $('.js-select').select2();
            })

        });
    </script>
@endpush
