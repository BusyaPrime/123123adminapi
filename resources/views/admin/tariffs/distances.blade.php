@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => "Тарифы", 'bodyClass' => 'card-body-no-padding'])
    
    @slot('buttons')
        <div class="row col-sm-6 justify-content-end">
            <a href="{{ route('admin.regions.manageTariffs') }}" class="btn btn-success ml-2">
                <span class="d-none d-sm-inline-block">Управление тарифами</span> <i class="fas fa-icmn-plus"></i>
            </a>
            <a href="{{ route('admin.regions.tariffsExportAll') }}" class="btn btn-warning ml-2">
                <span class="d-none d-sm-inline-block">Экспорт (тарифы)</span> <i class="icmn-plus"><!-- --></i>
            </a>
            <a href="{{ route('admin.regions.tariffsExport') }}" class="btn btn-default ml-2">
                <span class="d-none d-sm-inline-block">Экспорт (цены)</span> <i class="icmn-plus"><!-- --></i>
            </a>
        </div>
    @endslot
    
    <div class="row">
        @foreach($seasons as $season)
            <a href="{{ route('admin.regions.distances', ['season_id' => $season->id]) }}" class="btn {{ $season_id == $season->id ? 'btn-primary' : 'btn-default' }} ml-2">
                <span class="d-none d-sm-inline-block">{{ $season->title }}</span> <i class="fas fa-icmn-plus"></i>
            </a>
        @endforeach
    </div>
    <br />

    <div class="row" style="font-size:11px!important;">
        <div class="col">

    <table style="border-spacing: 0;" class="table table-bordered table-hover" cellspacing="0" cellpadding="0">
        <thead>
            <td style="padding:10px 25px;"></td>
            @foreach ($regionsCollection as $region)
                <td style="vertical-align:center; padding:5px 10px;">
                    <div>{{ $region->title }}</div>
                </td>
            @endforeach
        </thead>
        <form action="{{ route('admin.regions.distances.updateDistances') }}" method="POST">
            @method('PUT')
            @csrf
            @foreach ($distances as $distance)
                <tr>
                    @foreach ($distance as $rate)
                        @if ($loop->index == 0)
                            <td class='td'>
                                <div class="row">
                                    <div class="col p-2">
                                        {{ $rate->title }}
                                    </div>
                                </div>
                            </td>
                        @else
                            <td class='td changePrice'>
                                <div class="row">
                                    <div class="col p-2">
                                        <input class="form-control rounded-0 border-0 inputPrice text-right" type="number" name='distances[]' autocomplete="off" step='1' min='0' placeholder="{{ $rate->distance_between }}" />
                                        <input type="hidden" name='region_from_ids[]' value="{{ $rate->region_from_id }}" />
                                        <input type="hidden" name='region_to_ids[]' value="{{ $rate->region_to_id }}" />
                                        <input type="hidden" name='car_type_id' value="{{ $rate->car_type_id }}" />
                                        <input type="hidden" name='season_id' value="{{ $rate->season_id }}" />
                                    </div>
                                </div>
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
            <tr>
                <td colspan="{{ $distances->count() + 1 }}" align="right" style="padding: 10px 25px;">
                    <button class='btn btn-primary' type="submit">Сохранить изменения</button>
                </td>
            </tr>
        </form>
    </table>
        </div>
    </div>

    @endcomponent
@endsection