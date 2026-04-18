@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Статистика / Аналитика', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')
            <div class="row col-sm-6 justify-content-end">
                <form id="statistics_form" class="col-12 row" action="{{ route('admin.statistics.index') }}">

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
            </div>

        @endslot


        <div class="row mb-3">
            <div class="col ">
                <a href="{{ route('admin.statistics.bookings.year') }}" style="color: #000">
                    <div class="d-flex align-items-center p-2 border rounded">
                        <div class="mr-3">
                            <i class="fa fa-check-circle fa-3x text-success"></i>
                        </div>
                        <div>
                            <span>Заказы</span>
                            <h3 class="d-block ">{{ number_format($bookings_count ?? 0, 0, '', ' ') }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('admin.statistics.bookings.year') }}" style="color: #000">
                    <div class="d-flex align-items-center p-2 border rounded">
                        <div class="mr-3">
                            <i class="fa fa-times-circle fa-3x text-danger"></i>
                        </div>
                        <div>
                            <span>Отмены</span>
                            <h3 class="d-block ">{{ number_format($bookings_canceled_count ?? 0, 0, '', ' ') }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('admin.statistics.users.year') }}" style="color: #000">
                    <div class="d-flex align-items-center p-2 border rounded">
                        <div class="mr-3">
                            <i class="fa fa-user-circle fa-3x text-primary"></i>
                        </div>
                        <div>
                            <span>Пользователи</span>
                            <h3 class="d-block ">{{ number_format($users_count ?? 0, 0, '', ' ') }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('admin.statistics.users.year') }}" style="color: #000">
                    <div class="d-flex align-items-center p-2 border rounded">
                        <div class="mr-3">
                            <i class="fa fa-user-circle fa-3x text-cyan"></i>
                        </div>
                        <div>
                            <span>Водители</span>
                            <h3 class="d-block ">{{ number_format($drivers_count ?? 0, 0, '', ' ') }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('admin.statistics.users.year') }}" style="color: #000">
                    <div class="d-flex align-items-center p-2 border rounded">
                        <div class="mr-3">
                            <i class="fa fa-copyright fa-3x text-cyan"></i>
                        </div>
                        <div>
                            <span>Компании</span>
                            <h3 class="d-block ">{{ number_format($companies_count ?? 0, 0, '', ' ') }}</h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">

                <div class="d-flex p-2 border rounded ">
                    <div class="mr-3 d-flex flex-column align-items-center justify-content-between">
                        <div>
                            Оборот
                        </div>
                        <h3 class="d-block ">{{ number_format($bookings_sum ?? 0, 0, '', ' ') }}</h3>
                        <a href="{{ route('admin.statistics.sums.year') }}" class="btn btn-default">Подробнее</a>
                    </div>
                    <div>
                        <div class="text-center">{{ $year }}</div>
                        <canvas class="chart" id="sum_chart" width="400" height="150"></canvas>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="d-flex p-2 border rounded ">
                    <div class="mr-3 d-flex flex-column align-items-center justify-content-between">
                        <div>
                            Отчисления
                        </div>
                        <h3 class="d-block ">{{ number_format($bookings_commission ?? 0, 0, '', ' ') }}</h3>
                        <a href="{{ route('admin.statistics.sums.year') }}" class="btn btn-default">Подробнее</a>
                    </div>
                    <div>
                        <div class="text-center">{{ $year }}</div>
                        <canvas class="chart" id="commission_chart" width="400" height="150"></canvas>
                    </div>
                </div>

            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Популярные направления</p>
                    @if($popular_directions->isNotEmpty())
                        <table class="w-100">
                            @foreach($popular_directions as $popular_direction)
                                @php
                                    $regionFrom = \App\Domain\Regions\Models\Region::find($popular_direction->region_from_id);
                                    $regionTo = \App\Domain\Regions\Models\Region::find($popular_direction->region_to_id);
                                @endphp
                                <tr>
                                    <td class="text-right " style="font-size: 12px;">{{ $regionFrom->title ?? 'Не определено' }} - {{ $regionTo->title ?? 'Не определено' }}</td>
                                    <td class="w-50">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: {{ ($bookings_count > 0) ? round($popular_direction->cnt * 100 / $bookings_count, 2) : 0 }}%;" aria-valuenow="{{ ($bookings_count > 0) ? round($popular_direction->cnt * 100 / $bookings_count, 2) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td class="small">
                                        {{ ($bookings_count > 0) ? round($popular_direction->cnt * 100 / $bookings_count, 2) : 0 }}%
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.statistics.directions.graphic') }}" class="btn btn-default">Подробнее</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Популярные точки отправки</p>
                    <div>
                        <canvas class="chart" id="region_from_chart" height="205" ></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.statistics.regions.graphic') }}" class="btn btn-default">Подробнее</a>
                    </div>
                </div>
            </div>
            <div class="col">

                <div class=" p-2 border rounded ">
                    <p class="text-center">Популярные точки доставки</p>
                    <div>
                        <canvas class="chart" id="region_to_chart"  height="205"></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.statistics.regions.graphic') }}" class="btn btn-default">Подробнее</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Популярные виды транспорта</p>
                    <div class="row">
                        <div class="col-md-6">
                            <canvas class="chart" id="car_types_chart" style="position: relative;z-index: 900" ></canvas>
                        </div>
                        <div class="col-md-6">
                            @if($popular_car_types->isNotEmpty())
                                <table class="w-100">
                                    @foreach($popular_car_types as $popular_car_type)
                                        @php
                                            $carType = \App\Domain\CarTypes\Models\CarType::find($popular_car_type->car_type_id);
                                        @endphp
                                        <tr>
                                            <td class="text-left small" style="word-break: break-all">{{ $carType->title ?? 'Не указано' }}</td>
                                            <td class="text-right"> {{ ($bookings_count > 0) ? round($popular_car_type->cnt * 100 / $bookings_count, 2) : 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Популярные типы грузов</p>
                    <div class="row">
                        <div class="col-md-6">
                            <canvas class="chart" id="cargo_types_chart" style="position: relative;z-index: 900" ></canvas>
                        </div>
                        <div class="col-md-6">
                            @if($popular_cargo_types->isNotEmpty())
                                <table class="w-100">
                                    @foreach($popular_cargo_types as $popular_cargo_type)
                                        @php
                                            $cargoType = \App\Domain\CargoTypes\Models\CargoType::find($popular_cargo_type->cargo_type_id);
                                        @endphp
                                        <tr>
                                            <td class="text-left small" style="word-break: break-all">{{ $cargoType->title ?? 'Не указано' }}</td>
                                            <td class="text-right"> {{ ($bookings_count > 0) ? round($popular_cargo_type->cnt * 100 / $bookings_count, 2) : 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Кол. пользователей по регионам</p>
                    @if($user_by_region_count->isNotEmpty())
                        <table class="w-100">
                            @foreach($user_by_region_count as $user_by_region)
                                @php
                                    $region = \App\Domain\Regions\Models\Region::find($user_by_region->region_id);
                                @endphp
                            @if(isset($region->title))
                                <tr>
                                    <td class="text-right " style="font-size: 12px;">{{ $region->title ?? 'Не определено' }}</td>
                                    <td class="w-50">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: {{ ($users_count > 0) ? round($user_by_region->cnt * 100 / $users_count, 2) : 0 }}%;" aria-valuenow="{{ ($users_count > 0) ? round($user_by_region->cnt * 100 / $users_count, 2) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td class="small">
                                        {{ $user_by_region->cnt }}
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </table>
                    @endif
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.statistics.regions.users') }}" class="btn btn-default">Подробнее</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Причины отмены заказа</p>
                    <div>
                        <canvas class="chart" id="cancel_reason_chart"  height="205"></canvas>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.statistics.cancel-reasons') }}" class="btn btn-default">Подробнее</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center ">Среднее время</p>
                    <table class=" w-100" style="border-collapse: collapse">
                            <tr class="bg-primary">
                                <th class="px-2"></th>
                                <th class="px-2">Принятие</th>
                                <th class="px-2">Погрузки</th>
                                <th class="px-2">Разгрузки</th>
                            </tr>
                        @foreach($accepting_time as $title => $min)
                        <tr>
                            <td class="px-2 ">{{ $title }}</td>
                            <td class="px-2 ">{{ (int) $min ?? 0  }} мин</td>
                            <td class="px-2 ">{{ (int) $loading_time[$title] ?? 0  }} мин</td>
                            <td class="px-2 ">{{ (int) $unloading_time[$title] ?? 0  }} мин</td>
                        </tr>
                        @endforeach
                    </table>

                    <div class="text-center mt-3">
                        <a href="{{ route('admin.statistics.avg-time') }}" class="btn btn-default">Подробнее</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Соотношение полных и сборных грузов</p>
                    <table class="w-100">
                        <tr>
                            <td class="text-left small">Полных </td>
                            <td class="w-50">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ ($bookings_count > 0) ? round(($bookings_full_count ?? 0) * 100 / $bookings_count, 2) : 0 }}%;" aria-valuenow="{{ ($bookings_count > 0) ? round(($bookings_full_count ?? 0) * 100 / $bookings_count, 2) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                            <td class="text-right"> {{ ($bookings_count > 0) ? round(($bookings_full_count ?? 0) * 100 / $bookings_count, 2) : 0 }}%</td>
                        </tr>
                        <tr>
                            <td class="text-left small">Сборных </td>
                            <td class="w-50">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ ($bookings_count > 0) ? round(($bookings_not_full_count ?? 0) * 100 / $bookings_count, 2) : 0 }}%;" aria-valuenow="{{ ($bookings_count > 0) ? round(($bookings_not_full_count ?? 0) * 100 / $bookings_count, 2) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                            <td class="text-right"> {{ ($bookings_count > 0) ? round(($bookings_not_full_count ?? 0) * 100 / $bookings_count, 2) : 0 }}%</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col"></div>
            <div class="col"></div>
        </div>

        <h2 class="mt-5">Общая статистика</h2>
        <hr>
        <div class="row mt-3 mb-3">
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Рейтинг водителей</p>
                    <div class="row">
                        <div class="col-md-6">
                            <canvas class="chart" id="drivers_ratings_chart" style="position: relative;z-index: 900" ></canvas>
                        </div>
                        <div class="col-md-6">
                            @if($drivers_ratings->isNotEmpty())
                                <table class="w-100">
                                    @foreach($drivers_ratings as $rating => $count)
                                        <tr>
                                            <td class="text-left small" style="word-break: break-all"><i class="fas fa-star text-warning"></i> {{ $rating ?? 'Не указано' }}</td>
                                            <td class="text-right "> {{ ($drivers_total_count > 0) ? round($count * 100 / $drivers_total_count, 2) : 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Аналитика по аккаунтам</p>
                    <div class="row">
                        <div class="col-md-6">
                            <canvas class="chart" id="last_seen_at_chart" style="position: relative;z-index: 900" ></canvas>
                        </div>
                        <div class="col-md-6">
                            @if($last_seen_at->isNotEmpty())
                                <table class="w-100">
                                    @foreach($last_seen_at as $data => $count)
                                        <tr>
                                            <td class="text-left small {{$data == 'Неделя'? 'text-cyan': ($data == 'Месяц'? 'text-warning': 'text-danger')}} " style="word-break: break-all">{{ $data ?? 'Не указано' }}</td>
                                            <td class="text-right {{$data == 'Неделя'? 'text-cyan': ($data == 'Месяц'? 'text-warning': 'text-danger')}}"> {{ ($users_total_count > 0) ? round($count * 100 / $users_total_count, 2) : 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif
                        </div>
                    </div>

                    <div class="text-center mt-4 mb-4">
                        <a href="{{ route('admin.statistics.accounts.drivers') }}" class="btn btn-default">Подробнее</a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Оборот за</p>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="w-100">
                                <tr>
                                    <td class="text-left small" style="word-break: break-all">Последний год</td>
                                    <td class="text-right "> {{ number_format($bookings_sum_year_total ?? 0, 0, '', ' ') }} сум</td>
                                </tr>
                                <tr>
                                    <td class="text-left text-cyan small" style="word-break: break-all">Последний месяц</td>
                                    <td class="text-right text-cyan"> {{ number_format($bookings_sum_month_total ?? 0, 0, '', ' ') }} сум</td>
                                </tr>
                                <tr>
                                    <td class="text-left text-warning small" style="word-break: break-all">Последнюю неделю</td>
                                    <td class="text-right text-warning"> {{ number_format($bookings_sum_week_total ?? 0, 0, '', ' ') }} сум</td>
                                </tr>
                                <tr>
                                    <td class="text-left text-success small" style="word-break: break-all">Сегодня</td>
                                    <td class="text-right text-success"> {{ number_format($bookings_sum_day_total ?? 0, 0, '', ' ') }} сум</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--        @slot('bottom')--}}
        {{--            @include('ui.pagination', ['data' => $users])--}}
        {{--        @endslot--}}
    @endcomponent

@endsection

@push('styles')
    <style>
        /*.chart {*/
        /*    width: 100%;*/
        /*}*/
        /*td {*/
        /*    word-break: break-all;*/
        /*}*/
    </style>
@endpush


@push('scripts')
    <script src="{{ asset('vendor/chartjs/dist/chart.min.js') }}"></script>
    <script>
        var commissionChartBlock = document.getElementById('commission_chart').getContext('2d');
        var commissionChart = new Chart(commissionChartBlock, {
            type: 'line',
            data: {
                labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                datasets: [{
                    {{--label: '{{$page_title ?? ''}}',--}}
                    data: [
                        @for($i = 1; $i <= 12; $i++)
                            {{ $bookings_commission_chart['count_by_date'][$i] }},
                        @endfor
                    ],
                    fill: true,
                    tension: 0.5,
                    backgroundColor: 'rgb(248, 195, 16, 0.2)',
                    borderColor: 'rgb(248, 195, 16)'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
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
    <script>
        var sumChartBlock = document.getElementById('sum_chart').getContext('2d');
        var sumChart = new Chart(sumChartBlock, {
            type: 'line',
            data: {
                labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                datasets: [{
                    {{--label: '{{$page_title ?? ''}}',--}}
                    data: [
                        @for($i = 1; $i <= 12; $i++)
                            {{ $bookings_sum_chart['count_by_date'][$i] }},
                        @endfor
                    ],
                    fill: true,
                    tension: 0.5,
                    backgroundColor: 'rgb(248, 195, 16, 0.2)',
                    borderColor: 'rgb(248, 195, 16)'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
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

    <script>
        var regionFromBlock = document.getElementById('region_from_chart').getContext('2d');

        var labels = [];
        var data = [];

        @if($popular_regions_from->isNotEmpty())
            @foreach($popular_regions_from as $popular_region_from)
            @php
                $regionFrom = \App\Domain\Regions\Models\Region::find($popular_region_from->region_from_id);
            @endphp
            labels[{{$loop->index}}] = '{{ $regionFrom->title ?? 'Не определено' }}';
        data[{{$loop->index}}] = {{round($popular_region_from->cnt)}};
        @endforeach
        @endif


        var regionFromChart = new Chart(regionFromBlock, {
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
                responsive: true,
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

    <script>
        var regionToBlock = document.getElementById('region_to_chart').getContext('2d');

        var labels = [];
        var data = [];

        @if($popular_regions_to->isNotEmpty())
            @foreach($popular_regions_to as $popular_region_to)
            @php
                $regionTo = \App\Domain\Regions\Models\Region::find($popular_region_to->region_to_id);
            @endphp
            labels[{{$loop->index}}] = '{{ $regionTo->title ?? 'Не определено' }}';
        data[{{$loop->index}}] = {{round($popular_region_to->cnt)}};
        @endforeach
        @endif

        var regionToChart = new Chart(regionToBlock, {
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
                responsive: true,
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

    <script>
        var carTypesBlock = document.getElementById('car_types_chart').getContext('2d');

        var labels = [];
        var data = [];
        var colors = [];

        @if($popular_car_types->isNotEmpty())
            @foreach($popular_car_types as $popular_car_type)
            @php
                $carType = \App\Domain\CarTypes\Models\CarType::find($popular_car_type->car_type_id)
            @endphp
            labels[{{$loop->index}}] = '{{ $carType->title ?? 'Не указано' }}';
        data[{{$loop->index}}] = {{ $popular_car_type->cnt ?? 0 }};
        colors[{{$loop->index}}] = 'rgb({{mt_rand(0, 255)}}, {{mt_rand(0, 255)}}, {{mt_rand(0, 255)}})';
        @endforeach
        @endif

        var carTypesChart = new Chart(carTypesBlock, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    {{--label: '{{$page_title ?? ''}}',--}}
                    data: data,
                    fill: true,
                    backgroundColor: colors,
                    // borderColor: 'rgb(248, 195, 16)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>

    <script>
        var cargoTypesBlock = document.getElementById('cargo_types_chart').getContext('2d');

        var labels = [];
        var data = [];
        var colors = [];

        @if($popular_cargo_types->isNotEmpty())
            @foreach($popular_cargo_types as $popular_cargo_type)
            @php
                $cargoType = \App\Domain\CargoTypes\Models\CargoType::find($popular_cargo_type->cargo_type_id);
            @endphp
            labels[{{$loop->index}}] = '{{ $cargoType->title ?? 'Не указано' }}';
        data[{{$loop->index}}] = {{ $popular_cargo_type->cnt ?? 0 }};
        colors[{{$loop->index}}] = 'rgb({{mt_rand(0, 255)}}, {{mt_rand(0, 255)}}, {{mt_rand(0, 255)}})';
        @endforeach
        @endif

        var cargoTypesChart = new Chart(cargoTypesBlock, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    {{--label: '{{$page_title ?? ''}}',--}}
                    data: data,
                    fill: true,
                    backgroundColor: colors,
                    // borderColor: 'rgb(248, 195, 16)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>

    <script>
        var driversRatingsBlock = document.getElementById('drivers_ratings_chart').getContext('2d');

        var labels = [];
        var data = [];
        var colors = [];

        @if($drivers_ratings->isNotEmpty())
            @foreach($drivers_ratings as $rating => $count)
            labels[{{$loop->index}}] = '{{ $rating ?? 'Не указано' }}';
        data[{{$loop->index}}] = {{ $count ?? 0 }};
        colors[{{$loop->index}}] = 'rgb({{mt_rand(0, 255)}}, {{mt_rand(0, 255)}}, {{mt_rand(0, 255)}})';
        @endforeach
        @endif

        var driversRatingsChart = new Chart(driversRatingsBlock, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    {{--label: '{{$page_title ?? ''}}',--}}
                    data: data,
                    fill: true,
                    backgroundColor: colors,
                    // borderColor: 'rgb(248, 195, 16)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>

    <script>
        var lastSeenAtBlock = document.getElementById('last_seen_at_chart').getContext('2d');

        var labels = [];
        var data = [];
        var colors = [];

        @if($last_seen_at->isNotEmpty())
            @foreach($last_seen_at as $date => $count)
            labels[{{$loop->index}}] = '{{ $date ?? 'Не указано' }}';
        data[{{$loop->index}}] = {{ $count ?? 0 }};
        colors[{{$loop->index}}] = 'rgb({{ $date == 'Неделя'? '23, 162, 184': ($date == 'Месяц'? '255, 193, 7': '220, 53, 69') }})';
        @endforeach
        @endif

        var lastSeenAtChart = new Chart(lastSeenAtBlock, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    {{--label: '{{$page_title ?? ''}}',--}}
                    data: data,
                    fill: true,
                    backgroundColor: colors,
                    // borderColor: 'rgb(248, 195, 16)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
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
                responsive: true,
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
