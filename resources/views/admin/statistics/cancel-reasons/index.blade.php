@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Причины отмены заказа', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')
            <div class="row col-sm-6 justify-content-end">
                <form id="statistics_form" class="col-12 row" action="{{ route('admin.statistics.cancel-reasons') }}">

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
                <a href="{{ route('admin.statistics.cancel-reasons.export', ['preset' => $preset, 'date_start' => $date_start, 'date_end'=> $date_end]) }}" class="btn btn-default mt-3 mr-3">
                    <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
                </a>
            </div>

        @endslot







        <div class="row mb-3">
            <div class="col-6 offset-3">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Причины отмены заказа</p>
                    <div>
                        <canvas class="chart" id="cancel_reason_chart"  height="205"></canvas>
                    </div>
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
                                <th class="table__th">Дата</th>
                                <th class="table__th">Точка А/Точка Б</th>
                                <th class="table__th">Клиент</th>
                                <th class="table__th">Водитель</th>
                                <th class="table__th">Тип авто</th>
                                <th class="table__th">Тип груза</th>
                                <th class="table__th">Причина отмены</th>
                                <th class="table__th">Комментарий</th>
                            </tr>
                            </thead>
                            <tbody class="table__tbody">
                            @foreach($bookings as $booking)
                                <tr class="table__tr mt-2 mb-2 ">
                                    <td class="table__td">{{ $booking->id }}</td>
                                    <td class="table__td ">
                                        {{ $booking->created_at ? $booking->created_at->format('d.m.Y H:i') : '--' }}
                                    </td>
                                    <td class="table__td">
                                        @php
                                            $routes = json_decode($booking->routes ?? '', true);
                                                $regionFrom = isset($routes[0]) && isset($routes[0]['address']) ? $routes[0]['address'] : 'Адрес не указан';
                                                $regionTo = isset($routes[1]) && isset($routes[1]['address']) ? $routes[1]['address'] : 'Адрес не указан';
                                        @endphp
                                        {{ $regionFrom ?? 'Регион не определен' }} <span class="small"><i class="fas fa-arrow-right "></i></span> {{ $regionTo ?? 'Регион не определен' }}
                                    </td>
                                    <td class="table__td">
                                        {{ ($booking->user->surname ?? '').' '.($booking->user->name ?? '').' '.($booking->user->middle_name ?? '') }}
                                    </td>

                                    <td class="table__td ">
                                        {!! $booking->driver_id ? (($booking->driver->surname ?? '').' '.($booking->driver->name ?? '').' '.($booking->driver->middle_name ?? '')) : 'Водитель не назначен' !!}
                                    </td>
                                    <td class="table__td ">
                                        {{ $booking->carType->title ?? 'Тип не указан' }}
                                    </td>
                                    <td class="table__td">

                                        {{ $booking->cargoType->title ?? 'Тип не указан' }} / {{ round(($booking->weight ?? 0) / 1000, 3) }} т.

                                    </td>
                                    <td class="table__td ">
                                        {{ $booking->cancelReason->reason ?? 'Другое' }}
                                    </td>
                                    <td class="table__td ">
                                        {!! nl2br($booking->cancel_reason_comment ?? '') !!}
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>


        @slot('bottom')

            <div class="px-5 py-3 text-center">
                @include('ui.pagination', ['data' => $bookings])
            </div>

            <div class="px-5 py-3 text-center">
                <a href="{{ route('admin.statistics.index') }}" class="btn  btn-primary ">Вернуться в общую статистику</a>
            </div>
        @endslot

        {{--        @slot('bottom')--}}
        {{--            @include('ui.pagination', ['data' => $users])--}}
        {{--        @endslot--}}
    @endcomponent

@endsection



@push('scripts')
    <script src="{{ asset('vendor/chartjs/dist/chart.min.js') }}"></script>
    <script>
        var cancelReasonBlock = document.getElementById('cancel_reason_chart').getContext('2d');

        var labels = [];
        var data = [];

        @if($cancel_reason_count->isNotEmpty())
            @foreach($cancel_reason_count as $cancel_reason_title => $cancel_reason_cnt)
            labels[{{$loop->index}}] = '{{ $cancel_reason_title ?? 'Не определено' }}';
        data[{{$loop->index}}] = {{round($cancel_reason_cnt)}};
        @endforeach
        @endif

        var cancelReasonChart = new Chart(cancelReasonBlock, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    {{--label: '{{$page_title ?? ''}}',--}}
                    data: data,
                    fill: true,
                    backgroundColor: 'rgb(248, 195, 16)',
                    borderColor: 'rgb(248, 195, 16)'
                }]
            },
            options: {
                // responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
@endpush

@push('scripts')
    <script>
        $(function () {
            $('.js-presets').on('change', function () {
                $('#statistics_form').submit();
            });
        });
    </script>
@endpush
