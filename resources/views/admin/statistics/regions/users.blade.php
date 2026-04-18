@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Кол. пользователей по регионам', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')
            <div class="row col-sm-6 justify-content-end">
                <form id="statistics_form" class="col-12 row" action="{{ route('admin.statistics.regions.users') }}">

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
            <div class="col-8 offset-2">
                <div class=" p-2 border rounded ">
                    <canvas class="chart" id="users_by_region_chart"  height="205"></canvas>
                </div>
            </div>
        </div>

        @slot('bottom')
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
        var userByRegionBlock = document.getElementById('users_by_region_chart').getContext('2d');

        var labels = [];
        var data_drivers = [];
        var data_users = [];

        @php
            $index = 0;
        @endphp
        @if($user_by_region_divided->isNotEmpty())
            @foreach($user_by_region_divided as $k => $user_by_region)
            @php
                $region = \App\Domain\Regions\Models\Region::find($k);
            @endphp
            @if(isset($region->title))
            labels[{{$index}}] = '{{ $region->title ?? 'Не определено' }}';
            data_drivers[{{$index}}] = {{$user_by_region['drivers_count'] ?? 0}};
            data_users[{{$index}}] = {{$user_by_region['users_count'] ?? 0}};

            @php
                $index++;
            @endphp
            @endif
        @endforeach
        @endif

        var userByRegionChart = new Chart(userByRegionBlock, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Водители',
                    data: data_drivers,
                    fill: true,
                    backgroundColor: 'rgb(0,255,255)',
                    borderColor: 'rgb(0,255,255)'
                },
                    {
                        label: 'Пользователи',
                        data: data_users,
                        fill: true,
                        backgroundColor: 'rgb(0,255,89)',
                        borderColor: 'rgb(0,255,89)'
                    }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        beginAtZero: true,
                        stacked: true
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

@push('scripts')
    <script>
        $(function () {
            $('.js-presets').on('change', function () {
                $('#statistics_form').submit();
            });
        });
    </script>
@endpush
