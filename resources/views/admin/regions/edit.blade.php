@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.regions.update', $region) }}" method="post">
        @csrf
        @method('put')
        @component('component.card', ['title' => trans('admin.nav.regions').': '.trans('admin.editing')])
            <p class="text-center">Цена за км (сум)</p>
            <div class="row">
            @foreach(\App\Domain\CarTypes\Models\CarType::withTranslation()->get() as $carType)
                @php
                    $regionCarType = $region->carTypes()->find($carType->id);
                    if($regionCarType) {
                        $value = $regionCarType->pivot->price_per_km;
                    } else {
                        $value = 0;
                    }
                @endphp
                    <div class="col-md-4">
                        <div class="form-group row">
                            <label class="col-md-3 text-md-right col-form-label-sm" >{{ $carType->title }}</label>
                            <div class="col-md-9">
                                <input type="number" step="1" min="0" name="car_types[{{$carType->id}}][price_per_km]" class="form-control input-sm" value="{{ $value }}"  >
                            </div>
                        </div>
                    </div>
            @endforeach
            </div>
            @slot('bottom')

                <div class="px-5 py-3">
                <a href="{{ route('admin.regions.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.save')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection
