@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Оборот и отчисления', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')

            <div class="row col-sm-6 justify-content-end">
                <a  class="btn btn-primary active   mr-3">Год</a>
                <a href="{{ route('admin.statistics.sums.month') }}" class="btn btn-default mr-3">Месяц</a>
                <div class="col-5">
                    <form action="{{ url()->current() }}" id="statistics_form">
                        <div class="input-group ">
                            <input type="number" step="1"  name="date" value="{{ $date ?? '' }}" class="form-control"  />
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

        <div class="d-flex p-3 border rounded ">
            <div class="mr-5 d-flex flex-column  justify-content-between">
                <h3 class="d-block " style="color: #0000ff">
                    <div class="">
                        Оборот
                    </div>
                    {{ number_format($sums_total ?? 0, 0, '', ' ') }}</h3>
                <h3 class="d-block " style="color: #00ff00">
                    <div class="">
                        Отчисления
                    </div>
                    {{ number_format($commission_total ?? 0, 0, '', ' ') }}</h3>
            </div>
            <div>
                <canvas class="chart" id="myChart" width="900" height="300"></canvas>
            </div>
        </div>


{{--        <div class="card">--}}
{{--            <div class="card-body">--}}
{{--            </div>--}}
{{--        </div>--}}

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
                labels: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
                datasets: [{
                    label: 'Оборот',
                    data: [
                        @for($i = 1; $i <= 12; $i++)
                            {{ $sums_count['count_by_date'][$i] }},
                        @endfor
                    ],
                    fill: false,
                    tension: 0.5,
                    borderColor: 'rgb(0,0,255)'
                }, {
                    label: 'Отчисления',
                    data: [
                        @for($i = 1; $i <= 12; $i++)
                            {{ $commission_count['count_by_date'][$i] }},
                        @endfor
                    ],
                    fill: false,
                    tension: 0.5,
                    borderColor: 'rgb(0,255,89)'
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
