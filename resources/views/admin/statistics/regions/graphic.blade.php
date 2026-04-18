@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Популярные точки отправки и доставки', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')
<div class="row col-sm-6 justify-content-end">
    <form id="statistics_form" class="col-12 row" action="{{ route('admin.statistics.regions.graphic') }}">

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
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Популярные точки отправки</p>
                    <div>
                        <canvas class="chart" id="region_from_chart" height="205" ></canvas>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class=" p-2 border rounded ">
                    <p class="text-center">Популярные точки доставки</p>
                    <div>
                        <canvas class="chart" id="region_to_chart"  height="205"></canvas>
                    </div>
                </div>
            </div>
        </div>


{{--        @slot('bottom')--}}
{{--            @include('ui.pagination', ['data' => $users])--}}
{{--        @endslot--}}

        @slot('bottom')
            <div class="px-5 py-3 text-center">
                <a href="{{ route('admin.statistics.index') }}" class="btn  btn-primary ">Вернуться в общую статистику</a>
            </div>
        @endslot
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
