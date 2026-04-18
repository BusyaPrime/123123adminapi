@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Заказы и отмены', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')

            <div class="row col-sm-6 justify-content-end">
                <a href="{{ route('admin.statistics.bookings.year') }}" class="btn btn-default  mr-3">Год</a>
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

                <div class="row ">
                    <div class="col-md-3">
                        <div class="ways__box">
                            <div class="ways__name">Ожидающие</div>
                            <div class="ways__num">{{ $bookings_awaiting ?? 0 }}</div>
                            @if(in_array('bookings', $user_permissions))
                                <a href="{{ route('admin.bookings.index', ['status' => 'free']) }}" class="ways__btn">Подробнее</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ways__box">
                            <div class="ways__name">Выполняются</div>
                            <div class="ways__num">{{ $bookings_in_progress ?? 0 }}</div>
                            @if(in_array('bookings', $user_permissions))
                                <a href="{{ route('admin.bookings.index', ['status' => 'in_progress']) }}" class="ways__btn">Подробнее</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ways__box">
                            <div class="ways__name">Выполненные</div>
                            <div class="ways__num">{{ $bookings_done ?? 0 }}</div>
                            @if(in_array('bookings', $user_permissions))
                                <a href="{{ route('admin.bookings.index', ['status' => 'done']) }}" class="ways__btn">Подробнее</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ways__box">
                            <div class="ways__name">Отмены</div>
                            <div class="ways__num">{{ $bookings_cancels ?? 0 }}</div>
                            @if(in_array('bookings', $user_permissions))
                                <a href="{{ route('admin.bookings.index', ['status' => 'canceled']) }}" class="ways__btn">Подробнее</a>
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
                    @for($i = 1; $i <= $bookings_count['days_in_month']; $i++)
                        '{{ $i }}',
                    @endfor
                ],
                datasets: [{
                    label: 'Заказы',
                    data: [
                        @for($i = 1; $i <= $bookings_count['days_in_month']; $i++)
                            {{ $bookings_count['count_by_date'][$i] }},
                        @endfor
                    ],
                    fill: false,
                    tension: 0.5,
                    borderColor: 'rgb(0,255,89)'
                },
                    {
                        label: 'Отмены',
                        data: [
                            @for($i = 1; $i <= $bookings_cancel_count['days_in_month']; $i++)
                                {{ $bookings_cancel_count['count_by_date'][$i] }},
                            @endfor
                        ],
                        fill: false,
                        tension: 0.5,
                        borderColor: 'rgb(255,0,0)'
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
