@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Заказы и отмены', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')

            <div class="row col-sm-6 justify-content-end">
                <a href="{{ route('admin.statistics.users.year') }}" class="btn btn-default  mr-3">Год</a>
                <a class="btn btn-primary active  mr-3">Месяц</a>
                <div class="col-5">
                    <form action="{{ url()->current() }}" id="statistics_form">
                        <div class="input-group ">
                            <input type="month"  name="date" value="{{ isset($date) ? date('Y-m', strtotime($date)) :'' }}" class="form-control"  />
                            <span class="input-group-text p-0 border-0">
                                <button class="btn btn-success rounded-0" type="submit">
                                    <i class="fas fa-check"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>

        @endslot

        <div class="card">
            <div class="card-body">
                <canvas class="chart" id="myChart" width="400" height="125"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="col-md-12 ">
                    <div class="ways__name mb-4" style="color: #2b2b2b!important;font-weight: normal!important;">Водители</div>
                </div>
                <div class="row ">
                    <div class="col-md-4">
                        <div class="ways__box">
                            <div class="ways__name">Активные</div>
                            <div class="ways__num">{{ $drivers_active ?? 0 }}</div>
                            @if(in_array('cars', $user_permissions))
                                <a href="{{ route('admin.cars.index', ['active' => '1']) }}" class="ways__btn">Подробнее</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ways__box">
                            <div class="ways__name">На модерации</div>
                            <div class="ways__num">{{ $drivers_moderation ?? 0 }}</div>
                            @if(in_array('cars', $user_permissions))
                                <a href="{{ route('admin.cars.index', ['moderated' => '0']) }}" class="ways__btn">Подробнее</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ways__box">
                            <div class="ways__name">Заблокированные</div>
                            <div class="ways__num">{{ $drivers_blocked ?? 0 }}</div>
                            @if(in_array('cars', $user_permissions))
                                <a href="{{ route('admin.cars.index', ['active' => '0']) }}" class="ways__btn">Подробнее</a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @slot('bottom')
            <div class="px-5 py-3 text-center">
                <a href="{{ route('admin.statistics.index') }}" class="btn  btn-primary ">Вернуться в общую статистику</a>
            </div>
        @endslot

    @endcomponent
@endsection

@push('scripts')
    <script src="{{ asset('vendor/chartjs/dist/chart.min.js') }}"></script>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    @for($i = 1; $i <= $users_count['days_in_month']; $i++)
                        '{{ $i }}',
                    @endfor
                ],
                datasets: [{
                    label: 'Пользователи',
                    data: [
                        @for($i = 1; $i <= $users_count['days_in_month']; $i++)
                            {{ $users_count['count_by_date'][$i] }},
                        @endfor
                    ],
                    fill: false,
                    tension: 0.5,
                    borderColor: 'rgb(0,255,0)'
                },
                    {
                        label: 'Водители',
                        data: [
                            @for($i = 1; $i <= $drivers_count['days_in_month']; $i++)
                                {{ $drivers_count['count_by_date'][$i] }},
                            @endfor
                        ],
                        fill: false,
                        tension: 0.5,
                        borderColor: 'rgb(0,196,255)'
                    },
                    {
                        label: 'Компании',
                        data: [
                            @for($i = 1; $i <= $companies_count['days_in_month']; $i++)
                                {{ $companies_count['count_by_date'][$i] }},
                            @endfor
                        ],
                        fill: false,
                        tension: 0.5,
                        borderColor: 'rgb(255,238,0)'
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
                // plugins: {
                //     legend: {
                //         display: false
                //     }
                // }
            }
        });
    </script>
@endpush
