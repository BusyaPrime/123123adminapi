<div class="row">
    <div class="col-sm-12">
        <div class="row  ">
            <div class="col-sm-7  ">
                <div class="row align-items-end">
                    <div class="form-group col-sm-6">
                        <label for="my-input">Из региона</label>
                        <select id="my-input" class="form-control js-link-from" name="region_from_id">
                            @foreach(\App\Domain\Regions\Models\Region::all() as $region)
                                <option value="{{$region->id}}" {{ $region_from_id == $region->id ? 'selected':'' }}>{{$region->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-6 ">
                        <label for="my-input-1">В регион</label>
                        <select id="my-input-1" class="form-control js-link-to" name="region_to_id">
                            @foreach(\App\Domain\Regions\Models\Region::all() as $region)
                                <option value="{{$region->id}}" {{ $region_to_id == $region->id ? 'selected':'' }}>{{$region->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 d-flex justify-content-end align-items-center">
                @foreach(\App\Domain\Seasons\Models\Season::all() as $season)
                    <a href="{{ route('admin.car-types.rates.edit', [$car_type, 'season_id' => $season->id, 'region_from_id' => $region_from_id, 'region_to_id' => $region_to_id]) }}" class="btn  ml-4 {{ $season->id == $season_id ? 'btn-primary' :'btn-default' }}" data-toggle="tooltip" data-placement="top" title="{{ trans('admin.months.'.$season->month_start).' - '.trans('admin.months.'.$season->month_end) }}" >{{ $season->title }}</a>
                @endforeach
                <input type="hidden" name="season_id" value="{{$season_id}}">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-7">

    </div>
</div>
<hr>
<section class="row ">
        <div class="col-sm-6 mb-4">
            <div class="mb-3">
                <h5>Ценообразование по весу груза</h5>
            </div>

            <div class="border  p-3 rounded ">

                <div class="row  ">
                    <div class="col-sm-12">
                        <div class="row">
                            {{--            <div class="col-md-6">--}}
                            {{--                <div class="form-group">--}}
                            {{--                    <label >Лимит (км)</label>--}}
                            {{--                    <input type="number" step="1" min="0" name="distance" class="form-control" placeholder="0" value="{{ $rate->distance ?? 0 }}">--}}
                            {{--                </div>--}}
                            {{--            </div>--}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Коэффициент объемного веса</label>
                                    <input type="number" step="1" min="0" name="divider" class=" form-control" placeholder="0" value="{{ $rate->divider ?? 0 }}">
                                </div>
                            </div>
                            <div class="col-md-6">
{{--                                <div class="form-group">--}}
{{--                                    <label >Тариф</label>--}}
{{--                                    <input type="number" step="any" min="0" name="ratio" class=" form-control" placeholder="0" value="{{ $rate->ratio ?? 0 }}">--}}
{{--                                </div>--}}
                            </div>
                        </div>
                        <div class="row">
                            {{--            <div class="col-md-6">--}}
                            {{--                <div class="form-group">--}}
                            {{--                    <label >Лимит (км)</label>--}}
                            {{--                    <input type="number" step="1" min="0" name="distance" class="form-control" placeholder="0" value="{{ $rate->distance ?? 0 }}">--}}
                            {{--                </div>--}}
                            {{--            </div>--}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Цена за подачу авто <small class="text-muted">(полный)</small></label>
                                    <input type="number" step="1" min="0" name="min_price" class=" form-control" placeholder="Сум" value="{{ $rate->min_price ?? 0 }}">
                                </div>
                            </div>
                            {{--            <div class="col-md-6">--}}
                            {{--                <div class="form-group">--}}
                            {{--                    <label >Лимит (км)</label>--}}
                            {{--                    <input type="number" step="1" min="0" name="distance" class="form-control" placeholder="0" value="{{ $rate->distance ?? 0 }}">--}}
                            {{--                </div>--}}
                            {{--            </div>--}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Цена за подачу авто <small class="text-muted">(частичный)</small></label>
                                    <input type="number" step="1" min="0" name="not_full_min_price" class=" form-control" placeholder="Сум" value="{{ $rate->not_full_min_price ?? 0 }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Цена за дополнительную точку</label>
                                    <input type="number" step="1" min="0" name="group_load_price" class=" form-control" placeholder="Сум" value="{{ $rate->group_load_price ?? 0 }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{--            <div class="col-md-6">--}}
                            {{--                <div class="form-group">--}}
                            {{--                    <label >Лимит (км)</label>--}}
                            {{--                    <input type="number" step="1" min="0" name="distance" class="form-control" placeholder="0" value="{{ $rate->distance ?? 0 }}">--}}
                            {{--                </div>--}}
                            {{--            </div>--}}

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Лимит частичной погрузки <small class="text-muted">(%)</small></label>
                                    <input type="number" step="1" min="0" max="100" name="not_full_min_value" class=" form-control" placeholder="0%" value="{{ $rate->not_full_min_value ?? 0 }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Коэффициент надбавки</label>
                                    <input type="number" step="any" min="0" name="not_full_ratio" class=" form-control" placeholder="0" value="{{ $rate->not_full_ratio ?? 0 }}">
                                </div>
                            </div>
							<div class="col-md-6">
                                <div class="form-group">
                                    <label >Туда и обратно <small class="text-muted">(%)</small></label>
                                    <input type="number" step="any" min="0" max="100" name="trip_discount" class=" form-control" placeholder="0" value="{{ $rate->trip_discount ?? 0 }}">
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Дистанция <small class="text-muted">(км)</small></label>
                                    <input type="number" step="any" name="distance_between" class=" form-control" placeholder="0" value="{{ $rate->distance_between ?? 0 }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Коэффициент свыше лимита <small class="text-muted"></small></label>
                                    <input type="number" step="any" name="over_limit_coeff" class="form-control" placeholder="0" value="{{ $rate->over_limit_coeff ?? 0 }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Коэффициент общего изменения <small class="text-muted"></small></label>
                                    <input type="number" step='any' min="0.1" name="common_coefficient" class=" form-control" value="{{ $rate->common_coefficient ?? 0 }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="col-sm-6 mb-4">
        <div class="mb-3">
            <h5>Ценообразование по дальности доставки</h5>
        </div>
        <div class="border  p-3 rounded text-center">
            <div class="row  ">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row justify-content-around">
                                <div class="col-sm-5">
                                    <label>Расстояние</label>
                                </div>
                                <div class="col-sm-5">
                                    <label>Тариф</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-sm-12  js-weight-row">
                            @php
                                if($rate) {
                                    $ratePricesDistance = json_decode($rate->prices_distance, true) ?? [];
                                } else {
                                    $ratePricesDistance = [];
                                }
                            @endphp
                            @if(count($ratePricesDistance) > 0)
                                @foreach($ratePricesDistance as $rate_price_distance)
                                    <div class="row justify-content-around js-weight-el">
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <input type="number" step="1"  name="prices_distance[distance][]" class="form-control" value="{{ $rate_price_distance['distance'] ?? 0}}" placeholder="В км">
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <input type="number" step="any" min="0"  name="prices_distance[sum][]" class="form-control" value="{{ $rate_price_distance['sum'] ?? 0}}" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row justify-content-around js-weight-el">
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <input type="number" step="1" name="prices_distance[distance][]" class="form-control" placeholder="В км">
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <input type="number" step="any" min="0"  name="prices_distance[sum][]" class="form-control" value="" placeholder="">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-success pl-4 pr-4 pt-0 pb-0 mt-4 js-add-weight" style="font-size: 22px;">+</button>
        </div>
    </div>

    <div class="col-sm-6 mb-4">
        <div class="mb-3">
            <h5>Временной коэффициент</h5>
        </div>
        <div class="row  ">
            <div class="col-12">
                <div class="border p-3 rounded text-center">
                    <div class="row  ">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row justify-content-around">
                                        <div class="col-sm-3">
                                            <label>Начало</label>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Конец</label>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Коэффициент</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-sm-12  js-ratio-row">
                                    @php
                                        if($rate) {
                                            $timeRatios = json_decode($rate->time_ratio, true) ?? [];
                                        } else {
                                            $timeRatios = [];
                                        }
                                    @endphp
                                    @if(count($timeRatios) > 0)
                                        @foreach($timeRatios as $time_ratio)
                                            <div class="row justify-content-around js-ratio-el">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <input type="time" name="time_ratio[start][]" class="form-control" placeholder="00:00" value="{{ $time_ratio['start'] }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <input type="time" name="time_ratio[end][]" class="form-control" placeholder="00:00" value="{{ $time_ratio['end'] }}">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <input type="number" step="0.1" name="time_ratio[ratio][]" class="form-control" placeholder="0.0" value="{{ $time_ratio['ratio'] }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row justify-content-around js-ratio-el">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="time" name="time_ratio[start][]" class="form-control" placeholder="00:00">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="time" name="time_ratio[end][]" class="form-control" placeholder="00:00">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="number" step="0.1" name="time_ratio[ratio][]" class="form-control" placeholder="0.0">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success pl-4 pr-4 pt-0 pb-0 mt-4 js-add-ratio" style="font-size: 22px;">+</button>
                </div>
            </div>
        </div>
    </div>

</section>


@push('scripts')
    <script>
        $(function () {
            $('.js-link-from').on('change', function () {
                $('#region_from_id').val($(this).val());
                $('#rates_from').submit();
            });
            $('.js-link-to').on('change', function () {
                $('#region_to_id').val($(this).val());
                $('#rates_from').submit();
            });
            $('.js-add-weight').on('click', function () {
                copyEl = $(this).parent().find('.js-weight-el').first();
                cloneEl = copyEl.clone();
                cloneEl.find('input').val('');
                $(this).parent().find('.js-weight-row').append(cloneEl);
            });
            $('.js-add-ratio').on('click', function () {
                copyEl = $(this).parent().find('.js-ratio-el').first();
                cloneEl = copyEl.clone();
                cloneEl.find('input').val('');
                $(this).parent().find('.js-ratio-row').append(cloneEl);
            });
        });
    </script>
@endpush
