@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.car-types.rates.edit', $car_type) }}" id="rates_from">
        <input type="hidden" id="region_from_id" name="region_from_id" value="{{ $region_from_id  }}">
        <input type="hidden" id="region_to_id" name="region_to_id" value="{{ $region_to_id  }}">
        <input type="hidden" name="season_id" value="{{ $season_id  }}">
    </form>
    <form action="{{ route('admin.car-types.rates.update', $car_type) }}"  method="post" enctype="multipart/form-data">
        @csrf
        @component('component.card', ['title' => 'Тарифы: '.$car_type->title ?? '--' ])
            @include('admin.car-types._form_rates')
            @slot('bottom')

                <div class="px-5 py-3">
                <a href="{{ route('admin.car-types.edit', $car_type) }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.save')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
