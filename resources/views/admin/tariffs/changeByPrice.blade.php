@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => "Тарифы (обновление)", 'bodyClass' => 'card-body-no-padding'])
    <div class="row">
        <div class="col">
            <div class="row col justify-content-end">
                @foreach($seasons as $season)
                    <a href="{{ route('admin.regions.changeByPrice', ['season_id' => $season->id]) }}" class="btn {{ $currentSeasonId == $season->id ? 'btn-primary' : 'btn-default' }} ml-2">
                        <span class="d-none d-sm-inline-block">{{ $season->title }}</span> <i class="fas fa-icmn-plus"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <hr />

    <table style="border-spacing: 0;" class="table table-bordered table-hover" cellspacing="0" cellpadding="0">
        <thead>
            <td style="padding:10px 25px;">
                <form action="{{ route('admin.regions.changeByPrice') }}" method="GET">
                    <select class="border-0" name="region_from_id" id="region_from_id" onchange="this.form.submit()" style="outline: none;">
                        @foreach ($regions as $region)
                            <option {{ $currentRegionID == $region->id ? 'selected' : '' }} value="{{ $region->id }}">{{ $region->title }}</option>
                        @endforeach
                    </select>
                </form>
            </td>
            @foreach ($carTypes as $cType)
                <td style="vertical-align:center; padding:5px 10px;">
                    <div>{{ $cType->title }}</div>
                </td>
            @endforeach
        </thead>
        <form action="{{ route('admin.regions.updateTariffByPrice') }}" method="POST">
            @method('PUT')
            @csrf
            @for ($i = 0; $i < $regions->count(); $i++)
                @if($currentRegionID == $regions[$i]->id) @continue @endif
                    <tr>
                        <td>
                            <div style="max-height:30px; overflow:hidden;">
                                {{ str_replace('Республика ', '', $regions[$i]->title) }}
                            </div>
                        </td>
                        @foreach ($carTypes as $carType)
                        @php
                            $rate = $carType->car_type_rates->where('region_to_id', $regions[$i]->id)->first();
                            if($rate){
                                $pricesDistance = json_decode($rate->prices_distance, true);
                                $rateRation = 0;

                                foreach ($pricesDistance as $pd) {
                                    if(isset($pd['distance'])){
                                        if($rate->distance_between <= $pd['distance']){
                                            $rateRation = $pd['sum'];
                                            break;
                                        }
                                    }
                                    $rateRation = $pd['sum'];
                                }
                                $calculatedPrice = ($rate->distance_between * $carType->max_weight * $rateRation) + $rate->min_price;
                                $calculatedPrice = number_format($calculatedPrice, 0, '', ' ');
                            }
                        @endphp
                            <td class="td changePrice {{ $request->has('changed_fields') ? (in_array($currentRegionID.'_'.$regions[$i]->id.'_'.$carType->id, $request->input('changed_fields')) ? 'focus' : '') : 'focus' }}">
                                @php
                                    $chfields = $request->input('changed_fields', []);
                                    $val = '';

                                    foreach ($chfields as $value) {
                                        if(array_key_exists($currentRegionID.'_'.$regions[$i]->id.'_'.$carType->id, $value)) $val = array_values($value)[0];
                                    }
                                @endphp
                                <input class="form-control rounded-0 border-0 inputPrice {{ $val != '' ? 'changed' : '' }}" type="text" name='prices[]' autocomplete="off" step='1' min='1' placeholder="{{ $calculatedPrice }}" />
                                <input type="hidden" name='region_to_ids[][region_to_id]' value="{{ $regions[$i]->id }}" />
                                <input type="hidden" name='car_type_ids[][car_type_id]' value="{{ $carType->id }}" />
                                <input type="hidden" name='season_id' value="{{ $currentSeasonId }}" />
                                <input type="hidden" name='region_from_id' value="{{ $currentRegionID }}" />
                            </td>
                        @endforeach
                    </tr>
            @endfor
            <tr>
                <td colspan="{{ $carTypes->count() + 1 }}" align="right" style="padding: 10px 25px;">
                    <button class='btn btn-primary' type="submit">Сохранить изменения</button>
                </td>
            </tr>
        </form>
    </table>

    @endcomponent
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ asset('vendor/inputmask/jquery.inputmask.bundle.js') }}"></script>

    <script type='text/javascript'>
        $(() => {
            $('input.inputPrice').inputmask({
                alias: 'numeric',
				placeholder: '0',
				groupSeparator: ' ',
				autoGroup: true,
				digits: 2,
				digitsOptional: true

            });

        });
    </script>
    
@endpush