@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Среднее время принятия и погрузки/разгрузки заказа', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')
            <div class="row col-sm-6 justify-content-end">
                <form id="statistics_form" class="col-12 row" action="{{ route('admin.statistics.avg-time') }}">

                    <div class="col-3">

                        <div class="form-group d-inline">
                            <select class="form-control js-presets" name="preset">
                                <option value="all" {{ ($preset == 'all') ? 'selected':'' }}>За все время</option>
                                <option value="custom" {{ ($preset == 'custom') ? 'selected':'' }}>Указать дату</option>
                                <option value="week" {{ ($preset == 'week') ? 'selected':'' }}>Неделя</option>
                                <option value="month" {{ ($preset == 'month') ? 'selected':'' }}>Месяц</option>
                                <option value="year" {{ ($preset == 'year') ? 'selected':'' }}>Год</option>
                            </select>
                        </div>

                    </div>
                    <div class="col-9">

                        <div class="input-group ">
                            <input type="date" {{ ($preset == 'custom') ? '':'readonly' }} name="date_start" value="{{ isset($date_start) ? date('Y-m-d', strtotime($date_start)) :'' }}" class="form-control"  />
                            <span class="input-group-text ">-</span>
                            <input type="date" {{ ($preset == 'custom') ? '':'readonly' }} name="date_end" value="{{ isset($date_end) ? date('Y-m-d', strtotime($date_end)) :'' }}" class="form-control"  />
                            <span class="input-group-text p-0 border-0">
                <button class="btn btn-success rounded-0" type="submit">
                    <i class="fas fa-check"></i>
                </button>
            </span>
                        </div>

                    </div>

                </form>

                {{--    <button type="button" class="btn btn-default  ml-2">Экспорт</button>--}}

                <a href="{{ route('admin.statistics.avg-time.export', ['preset' => $preset, 'date_start' => $date_start, 'date_end'=> $date_end]) }}" class="btn btn-default mt-3 mr-3">
                    <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
                </a>
            </div>

        @endslot

        <div class="row mb-3">
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center ">Среднее время принятия заказа</p>
                    <div class="table-responsive">
                        <table class=" w-100" style="border-collapse: collapse">
                            <tr class="bg-primary">
                                <th class="px-2"></th>
                                @foreach($accepting_time as $title => $byCargoTypes)
                                    @foreach($byCargoTypes as $cTTitle => $min)
                                        <th class="px-2 ">{{ $cTTitle }}</th>
                                    @endforeach
                                @endforeach
                            </tr>
                            @foreach($accepting_time as $title => $byCargoTypes)
                                <tr>
                                    <td class="px-2 ">{{ $title }}</td>
                                    @foreach($byCargoTypes as $cTTitle => $min)
                                        <td class="px-2 ">{{ (int) $min ?? 0  }} мин</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center ">Среднее время погрузки и разгрузки</p>
                    <table class=" w-100" style="border-collapse: collapse">
                        <tr class="bg-primary">
                            <th class="px-2"></th>
                            <th class="px-2">Погрузка</th>
                            <th class="px-2">Разгрузка</th>
                        </tr>
                        @foreach($loading_time as $title => $min)
                            <tr>
                                <td class="px-2 ">{{ $title }}</td>
                                <td class="px-2 ">{{ (int) $loading_time[$title] ?? 0  }} мин</td>
                                <td class="px-2 ">{{ (int) $unloading_time[$title] ?? 0  }} мин</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                @if($bookings->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table__thead">
                            <tr>
                                <th class="table__th">ID</th>
                                <th class="table__th">Точка А/Точка Б</th>
                                <th class="table__th">Тип груза</th>
                                <th class="table__th">Тип авто</th>
                                <th class="table__th">Время создания</th>
                                <th class="table__th">Время принятия</th>
                                <th class="table__th">Время погрузки</th>
                                <th class="table__th">Время разгрузки</th>
                                <th class="table__th">Время в пути</th>
                            </tr>
                            </thead>
                            <tbody class="table__tbody">
                            @foreach($bookings as $booking)
                                <tr class="table__tr mt-2 mb-2 ">
                                    <td class="table__td">{{ $booking->id }}</td>
                                    <td class="table__td">
                                        @php
                                            $routes = json_decode($booking->routes ?? '', true);
                                                $regionFrom = isset($routes[0]) && isset($routes[0]['address']) ? $routes[0]['address'] : 'Адрес не указан';
                                                $regionTo = isset($routes[1]) && isset($routes[1]['address']) ? $routes[1]['address'] : 'Адрес не указан';
                                        @endphp
                                        {{ $regionFrom ?? 'Регион не определен' }} <span class="small"><i class="fas fa-arrow-right "></i></span> {{ $regionTo ?? 'Регион не определен' }}
                                    </td>
                                    <td class="table__td">

                                        {{ $booking->cargoType->title ?? 'Тип не указан' }} / {{ round(($booking->weight ?? 0) / 1000, 3) }} т.

                                    </td>
                                    <td class="table__td ">
                                        {{ $booking->carType->title ?? 'Тип не указан' }}
                                    </td>

                                    <td class="table__td ">
                                        {{ $booking->created_at ? $booking->created_at->format('d.m.Y H:i') : '--' }}
                                    </td>

                                    <td class="table__td ">
                                        {{ $booking->accepting_time ? $booking->accepting_time.' мин' : '--' }}
                                    </td>

                                    <td class="table__td ">
                                        {{ $booking->pickup_waiting_time ? $booking->pickup_waiting_time.' мин' : '--' }}
                                    </td>

                                    <td class="table__td ">
                                        {{ $booking->unloading_waiting_time ? $booking->unloading_waiting_time.' мин' : '--' }}
                                    </td>

                                    <td class="table__td ">
                                        {{ $booking->driving_time ? $booking->driving_time.' мин' : '--' }}
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{--        @slot('bottom')--}}
        {{--            @include('ui.pagination', ['data' => $users])--}}
        {{--        @endslot--}}


        @slot('bottom')

            <div class="px-5 py-3 text-center">
                @include('ui.pagination', ['data' => $bookings])
            </div>

            <div class="px-5 py-3 text-center">
                <a href="{{ route('admin.statistics.index') }}" class="btn  btn-primary ">Вернуться в общую статистику</a>
            </div>
        @endslot
    @endcomponent

@endsection

@push('scripts')
    <script>
        $(function () {
            $('.js-presets').on('change', function () {
                $('#statistics_form').submit();
            });
        });
    </script>
@endpush
