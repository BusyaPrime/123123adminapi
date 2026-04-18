@extends('admin2.layout')

@section('center_content')
    @component('component.card', ['title' => 'Трекинг', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin2.tracking.index', ['has_order' => 'all']) }}" class="">
                    <div class="card {{ $filters['has_order'] == 'all'? 'bg-primary': '' }} d-inline-block m-0 ml-3">
                        <div class="card-body  p-3">
                            Все
                        </div>
                    </div>
                </a>
                <a href="{{ route('admin2.tracking.index', ['has_order' => 'no_order']) }}" class="">
                    <div class="card {{ $filters['has_order'] == 'no_order'? 'bg-primary': '' }} d-inline-block m-0 ml-3">
                        <div class="card-body p-3">
                            <img src="{{ asset('images/green-circle.png') }}" alt="Зеленые" class="d-inline-block" width="16px"> Свободные
                        </div>
                    </div>
                </a>
                <a href="{{ route('admin2.tracking.index', ['has_order' => 'has_order']) }}" class="">
                    <div class="card {{ $filters['has_order'] == 'has_order'? 'bg-primary': '' }} d-inline-block m-0 ml-3">
                        <div class="card-body p-3">
                            <img src="{{ asset('images/orange-circle.png') }}" alt="Зеленые" class="d-inline-block" width="16px"> На заказе
                        </div>
                    </div>
                </a>
            </div>

        @endslot

        <div class="row">
            <div class="col-md-12 mb-3">

                <div class="">
                    <div class="card m-0">
                        <div class="card-body p-3">
{{--                            <p>--}}
{{--                                Поиск по водителям--}}
{{--                            </p>--}}
                            <form action="{{ route('admin2.tracking.index') }}">
                                <input type="hidden" name="has_order" value="{{$filters['has_order']}}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" name="driver_phone" class="form-control" value="{{$filters['driver_phone'] ?? ''}}" placeholder="Номер телефона">
                                        </div></div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" name="car_number" class="form-control" value="{{$filters['car_number'] ?? ''}}" placeholder="Номер транспорта">
                                        </div></div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <select name="car_type_id" id="car_type_id" class="form-control">
                                                <option value="">Все типы транспорта</option>
                                                @foreach($carTypes as $car_type)
                                                    <option value="{{ $car_type->id }}" {{ $filters['car_type_id'] == $car_type->id? 'selected':'' }}>{{ $car_type->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <button class="btn btn-success" type="submit">Найти</button>
                                        <a href="{{ route('admin2.tracking.index', ['has_order' => 'all']) }}" class="btn btn-default">Сбросить фильтры</a>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if(false)
                <div class="">
                    <div class="card ">
                        <div class="card-body p-3">
                            <p>
                                Поиск по заказам
                            </p>
                            <form action="{{ route('admin2.tracking.index') }}">
                                <input type="hidden" name="search_type" value="orders">
                                <div class="form-group">
                                    <input type="text" name="order_id" class="form-control" placeholder="ID заказа">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="client_phone" class="form-control" placeholder="Номер телефона клиента">
                                </div>
                                <div class="text-right">
                                    <button class="btn btn-success" type="submit">Найти</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

            </div>
            <div class="col-md-12">
                <div id="map"></div>
            </div>
        </div>



    @endcomponent
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
                    center: [{{$center[0]}}, {{$center[1]}}],
                    zoom: 17
                }, {
                    searchControlProvider: 'yandex#search'
                });

                @foreach($onlineUsers as $item)
                    @if($item && $item->user->profile->lat && $item->user->profile->lng)
                        @php($orderCount = $item->active_orders_count ?? 0)
                        @if($filters['has_order'] == 'no_order' && $orderCount > 0)
                            @continue
                        @elseif($filters['has_order'] == 'has_order' && $orderCount <= 0)
                            @continue
                        @endif
                        myMap.geoObjects.add(
                            new ymaps.Placemark([{{$item->user->profile->lat}}, {{$item->user->profile->lng}}], {
                                hintContent: '{{$item->number ?? '--'}}',
                                balloonContent: '{{$item->number ?? '--'}} <br>{{ (($item->user->profile->surname ?? '').' '.($item->user->profile->name ?? '').' '.($item->user->profile->middle_name ?? '')) }} <br>+{{$item->user->username ?? '--'}}'
                            }, {
                                iconLayout: 'default#image',
                                iconImageSize: [20, 20],
                                @if($orderCount > 0)
                                iconImageHref: '{{ asset('images/orange-circle.png') }}',
                                @else
                                    {{--                        iconImageHref: '{{ ($item->user->last_seen_at && (now()->timestamp - $item->user->last_seen_at->timestamp < (5 * 60)))? asset('images/green-circle.png'): asset('images/red-circle.png')}}'--}}
                                iconImageHref: '{{ asset('images/green-circle.png') }}'
                                @endif
                            })
                        );
                    @endif
                @endforeach
            @if(false)
                var multiRoute = new ymaps.multiRouter.MultiRoute({
                    // Описание опорных точек мультимаршрута.
                    referencePoints: [
                        [41.326681, 69.244031],
                        [41.340681, 69.200031],
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
            @endif
            }
        });
    </script>
@endpush
