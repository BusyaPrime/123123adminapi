@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => "Тарифы (обновление)", 'bodyClass' => 'card-body-no-padding'])
    <div class="row">
        <div class="col">
            <div class="row col justify-content-end">
                @foreach($seasons as $season)
                    <a href="{{ route('admin.regions.manageTariffs', ['season_id' => $season->id]) }}" class="btn {{ $currentSeasonId == $season->id ? 'btn-primary' : 'btn-default' }} ml-2">
                        <span class="d-none d-sm-inline-block">{{ $season->title }}</span> <i class="fas fa-icmn-plus"></i>
                    </a>
                @endforeach
            </div>

        <div class="row mb-5">
            <div class="col-md-3">
                <form action="#" class="_search" onsubmit="return false;">
                    <label for="search-directions">Поиск направлений</label>
                    <input type="search" name='search' id="search-directions" placeholder="например: Ташкент-Самарканд" />
                </form>
            </div>
            <div class="col-md-3">
                <form action="{{ route('admin.regions.manageTariffs')}}">
                    <label>От</label>
                    <select class='form-control rounded-0' name="region_from" onchange='this.form.submit()'>
                        <option value="">Не задано</option>
                        @foreach($regions as $region)
                            <option {{ \Request::input('region_from', '') == $region->id ? 'selected' : '' }} value="{{ $region->id }}">{{ $region->title }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="region_to" value='{{ \Request::input('region_to', '') }}' />
                    <input type="hidden" name="currentSeasonId" value='{{ \Request::input('currentSeasonId', $currentSeasonId) }}' />
                </form>
            </div>
            <div class="col-md-3">
                <form action="{{ route('admin.regions.manageTariffs') }}">
                    <label>До</label>
                    <select name="region_to" class="form-control rounded-0" onchange='this.form.submit()'>
                        <option value="">Не задано</option>
                        @foreach($regions as $region)
                           <option {{ \Request::input('region_to', '') == $region->id ? 'selected' : '' }} value="{{ $region->id }}">{{ $region->title }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="region_from" value='{{ \Request::input('region_from', '') }}' />
                    <input type="hidden" name="currentSeasonId" value='{{ \Request::input('currentSeasonId', $currentSeasonId) }}' />
                </form>
            </div>
        </div>
                @include('admin.tariffs._form', ['currentSeasonId' => $currentSeasonId])
        </div>
    </div>

    <div class="row table-pagination">
        <div class="col-md-12 ">
            <ul class="pagination flex-wrap" role="navigation">
                    <li class="page-item"><a class="page-link" href="{{ route('admin.regions.manageTariffs', ['page' => 1]) }}"><i class="fas fa-arrow-right"></i></a></li>
                @for($i = 1; $i <= $paginationsCount; $i++)
                    @if($page == $i)
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $i }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ route('admin.regions.manageTariffs', ['page' => $i]) }}">{{ $i }}</a></li>
                    @endif
                @endfor
            </ul>
        </div>
    </div>

    @endcomponent
@endsection
