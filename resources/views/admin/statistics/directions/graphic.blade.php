@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Популярные направления', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')
<div class="row col-sm-6 justify-content-end">
    <form id="statistics_form" class="col-12 row" action="{{ route('admin.statistics.directions.graphic') }}">

        <input type="hidden" id="region_from_id_hidden" name="region_from_id" value="{{ $region_from_id }}">
        <input type="hidden" id="region_to_id_hidden" name="region_to_id" value="{{ $region_to_id }}">
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

    <a href="{{ route('admin.statistics.directions.graphic.export', ['preset' => $preset]) }}" class="btn btn-default mt-3 mr-3">
        <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
    </a>
{{--    <button type="button" class="btn btn-default  ml-2">Экспорт</button>--}}
</div>

        @endslot


<div class="row mb-3">
    <div class="col-6 offset-3">
        <div class="row align-items-end">
            <div class="form-group col-sm-6">
                <label for="my-input">Из региона</label>
                <select id="my-input" class="form-control js-link-from" name="region_from_id_select">
                    <option value="">Все</option>
                    @foreach(\App\Domain\Regions\Models\Region::all() as $region)
                        <option value="{{$region->id}}" {{ $region_from_id == $region->id ? 'selected':'' }}>{{$region->title}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-sm-6 ">
                <label for="my-input-1">В регион</label>
                <select id="my-input-1" class="form-control js-link-to" name="region_to_id_select">
                    <option value="">Все</option>
                    @foreach(\App\Domain\Regions\Models\Region::all() as $region)
                        <option value="{{$region->id}}" {{ $region_to_id == $region->id ? 'selected':'' }}>{{$region->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

        <div class="row mb-3">
            <div class="col">
                <div class=" p-2 border rounded ">
                    @if($popular_directions->isNotEmpty())
                        <table class="w-100">
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="text-center">
                                    <strong>Кол. заказов</strong>
                                </td>
                                <td></td>
                            </tr>
                            @foreach($popular_directions as $popular_direction)
                                @php
                                    $regionFrom = \App\Domain\Regions\Models\Region::find($popular_direction->region_from_id);
                                    $regionTo = \App\Domain\Regions\Models\Region::find($popular_direction->region_to_id);
                                @endphp
                                <tr>
                                    <td class="text-right pr-3" >{{ $regionFrom->title ?? 'Не определено' }} - {{ $regionTo->title ?? 'Не определено' }}</td>
                                    <td class="w-50">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: {{ ($bookings_count > 0) ? round($popular_direction->cnt * 100 / $bookings_count, 2) : 0 }}%;" aria-valuenow="{{ ($bookings_count > 0) ? round($popular_direction->cnt * 100 / $bookings_count, 2) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ $popular_direction->cnt }}
                                    </td>
                                    <td >
                                        {{ ($bookings_count > 0) ? round($popular_direction->cnt * 100 / $bookings_count, 2) : 0 }}%
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
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
@endpush

@push('scripts')
    <script>
        $(function () {
           $('.js-presets').on('change', function () {
               $('#statistics_form').submit();
           });
            $('.js-link-from').on('change', function () {
                $('#region_from_id_hidden').val($(this).val());
                $('#statistics_form').submit();
            });
            $('.js-link-to').on('change', function () {
                $('#region_to_id_hidden').val($(this).val());
                $('#statistics_form').submit();
            });

        });
    </script>
@endpush
