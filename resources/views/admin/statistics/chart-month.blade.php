@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Статистика / Аналитика: '.$page_title, 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')

            <div class="row col-sm-6 justify-content-end">
                <a href="{{ route('admin.statistics.chart.year', ['model' => $model]) }}" class="btn btn-default  mr-3">Год</a>
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
                    @for($i = 1; $i <= $days_in_month; $i++)
                        '{{ $i }}',
                    @endfor
                ],
                datasets: [{
                    label: '{{$page_title ?? ''}}',
                    data: [
                        @for($i = 1; $i <= $days_in_month; $i++)
                            {{ $count_by_date[$i] }},
                        @endfor
                    ],
                    fill: false,
                    borderColor: 'rgb(248, 195, 16)',
                    // backgroundColor: [
                    //     'rgba(255, 99, 132, 0.2)',
                    //     'rgba(54, 162, 235, 0.2)',
                    //     'rgba(255, 206, 86, 0.2)',
                    //     'rgba(75, 192, 192, 0.2)',
                    //     'rgba(153, 102, 255, 0.2)',
                    //     'rgba(255, 99, 132, 0.2)',
                    //     'rgba(54, 162, 235, 0.2)',
                    //     'rgba(255, 206, 86, 0.2)',
                    //     'rgba(75, 192, 192, 0.2)',
                    //     'rgba(153, 102, 255, 0.2)',
                    //     'rgba(255, 99, 132, 0.2)',
                    //     'rgba(255, 159, 64, 0.2)'
                    // ],
                    // borderColor: [
                    //     'rgba(255, 99, 132, 1)',
                    //     'rgba(54, 162, 235, 1)',
                    //     'rgba(255, 206, 86, 1)',
                    //     'rgba(75, 192, 192, 1)',
                    //     'rgba(153, 102, 255, 1)',
                    //     'rgba(255, 159, 64, 1)'
                    // ],
                    // borderWidth: 1
                }]
            },
            options: {
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
