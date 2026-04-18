<div class="form-group row {!! $errors->first('name', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="name">Имя</label>
    <div class="col-md-4">
        <input type="text" name="name" class="form-control input-sm" id="name" value="{{ old('name', $user->profile->name ?? '') }}" required autofocus>
        {!! $errors->first('name', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<div class="form-group row {!! $errors->first('surname', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="surname">Фамилия</label>
    <div class="col-md-4">
        <input type="text" name="surname" class="form-control input-sm" id="surname" value="{{ old('surname', $user->profile->surname ?? '') }}" >
        {!! $errors->first('surname', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<div class="form-group row {!! $errors->first('middle_name', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="middle_name">Отчество</label>
    <div class="col-md-4">
        <input type="text" name="middle_name" class="form-control input-sm" id="middle_name" value="{{ old('middle_name', $user->profile->middle_name ?? '') }}" >
        {!! $errors->first('middle_name', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<div class="form-group row {!! $errors->first('birthday', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="birthday">Дата рождения</label>
    <div class="col-md-4">
        <input type="text" name="birthday" class="form-control input-sm" id="birthday" value="{{ old('birthday', $user->profile->birthday ?? '') }}" >
        {!! $errors->first('birthday', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<hr>
<div class="form-group row {!! $errors->first('username', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="username">@lang('validation.attributes.username') </label>
    <div class="col-md-4">
        <input type="text" name="username" class="form-control input-sm" id="username" {{ isset($user) ? 'readonly': '' }} value="{{ old('username', $user->username ?? '') }}" required>
        {!! $errors->first('username', '<small class="form-control-feedback">:message</small>') !!}
    </div>
    <div class="col-md-5">Номер телефона без плюса в формате 998XX0000000</div>
</div>

{{--<div class="form-group row {!! $errors->first('company_id', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="company_id">Компания</label>--}}
{{--    <div class="col-md-4">--}}
{{--        <select name="company_id" id="company_id" class="form-control input-sm">--}}
{{--            <option value="">Самозанятый</option>--}}
{{--            @foreach(\App\Domain\Companies\Models\Company::get() as $company)--}}
{{--                <option value="{{ $company->id }}" {{ old('company_id', $user->profile->company_id ?? '') == $company->id? 'selected': '' }}>{{ $company->title }}</option>--}}
{{--            @endforeach--}}
{{--        </select>--}}
{{--        {!! $errors->first('company_id', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}
<input type="hidden" name="company_id" value="{{ $company->id }}">

<div class="form-group row {!! $errors->first('telegram', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="telegram">Telegram </label>
    <div class="col-md-4">
        <input type="text" name="telegram" class="form-control input-sm" id="telegram" value="{{ old('telegram', $user->profile->telegram ?? '') }}" >
        {!! $errors->first('telegram', '<small class="form-control-feedback">:message</small>') !!}
    </div>
    <div class="col-md-5">Username/Номер/Ссылка на Telegram поль-ля</div>
</div>
<div class="form-group row {!! $errors->first('email', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="email">Эл. почта </label>
    <div class="col-md-4">
        <input type="email" name="email" class="form-control input-sm" id="email" value="{{ old('email', $user->email ?? '') }}" >
        {!! $errors->first('email', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('region_id', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="region_id">Регион</label>
    <div class="col-md-4">
        <select name="region_id" id="region_id" class="form-control ">
            <option value="">Не указан</option>
            @foreach(\App\Domain\Regions\Models\Region::all() as $region)
                <option value="{{ $region->id }}" {{ old('region_id', $user->profile->region_id ?? '') == $region->id? 'selected':'' }}>{{ $region->title }}</option>
            @endforeach
        </select>
        {!! $errors->first('region_id', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<hr>
<div class="form-group row {!! $errors->first('car_type_id', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="car_type_id">@lang('validation.attributes.car_type_id')</label>
    <div class="col-md-4">
        <select name="car_type_id" id="car_type_id" class="form-control input-sm" required>
            @foreach(\App\Domain\CarTypes\Models\CarType::withTranslation()->get() as $item)
                <option value="{{ $item->id }}" {{ old('car_type_id', $car->car_type_id ?? null) == $item->id ? 'selected': '' }}>{{ $item->title }}</option>
            @endforeach
        </select>
        {!! $errors->first('car_type_id', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('load_type', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="load_type">@lang('validation.attributes.load_type')</label>
    <div class="col-md-4">
        <select name="load_type" id="load_type" class="form-control input-sm" required>
            @foreach(\App\Domain\LoadTypes\Models\LoadType::withTranslation()->get() as $item)
                <option value="{{ $item->id }}" {{ (in_array($item->id, array_keys(isset($car) ? $car->loadTypes->keyBy('id')->toArray(): [])))? 'selected' : '' }}>{{ $item->title }}</option>
            @endforeach
        </select>
        {!! $errors->first('load_type', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<div class="form-group row {!! $errors->first('brand', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="brand">Марка</label>
    <div class="col-md-4">
        <input type="text"   name="brand" class="form-control input-sm" id="brand" value="{{ old('brand', $car->brand ?? '') }}"  required>
        {!! $errors->first('brand', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<!-- <div class="form-group row {!! $errors->first('model', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="model">@lang('validation.attributes.model')</label>
    <div class="col-md-4">
        <input type="text"   name="model" class="form-control input-sm" id="model" value="{{ old('model', $car->model ?? '') }}" required >
        {!! $errors->first('model', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div> -->


<div class="form-group row {!! $errors->first('number', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="number">@lang('validation.attributes.number')</label>
    <div class="col-md-4">
        <input type="text"   name="number" class="form-control input-sm" id="number" value="{{ old('number', $car->number ?? '') }}" required >
        {!! $errors->first('number', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>



<div class="form-group row {!! $errors->first('trailer_number', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="trailer_number">Гос номер прицепа</label>
    <div class="col-md-4">
        <input type="text"   name="trailer_number" class="form-control input-sm" id="trailer_number" value="{{ old('trailer_number', $car->trailer_number ?? '') }}"  >
        {!! $errors->first('trailer_number', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

{{--<div class="form-group row {!! $errors->first('max_weight', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="max_weight">@lang('validation.attributes.max_weight') <small class="text-muted">(кг)</small></label>--}}
{{--    <div class="col-md-4">--}}
{{--        <input type="number" step="1" min="0" name="max_weight" class="form-control input-sm" id="max_weight" value="{{ old('max_weight', $car->max_weight ?? 0) }}" required >--}}
{{--        {!! $errors->first('max_weight', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="form-group row ">--}}
{{--    <div class="offset-md-3 col-md-6">--}}
{{--        <h5>@lang('validation.attributes.dimensions')</h5>--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="form-group row {!! $errors->first('dimension_x', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="dimension_x">@lang('validation.attributes.dimension_x') <small class="text-muted">(м)</small></label>--}}
{{--    <div class="col-md-4">--}}
{{--        <input type="number" step="0.01" min="0" name="dimension_x" class="form-control input-sm" id="dimension_x" value="{{ old('dimension_x', $car->dimension_x ?? 0) }}" required >--}}
{{--        {!! $errors->first('dimension_x', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}


{{--<div class="form-group row {!! $errors->first('dimension_y', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="dimension_y">@lang('validation.attributes.dimension_y') <small class="text-muted">(м)</small></label>--}}
{{--    <div class="col-md-4">--}}
{{--        <input type="number" step="0.01" min="0" name="dimension_y" class="form-control input-sm" id="dimension_y" value="{{ old('dimension_y', $car->dimension_y ?? 0) }}" required >--}}
{{--        {!! $errors->first('dimension_y', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}


{{--<div class="form-group row {!! $errors->first('dimension_z', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="dimension_z">@lang('validation.attributes.dimension_z') <small class="text-muted">(м)</small></label>--}}
{{--    <div class="col-md-4">--}}
{{--        <input type="number" step="0.01" min="0" name="dimension_z" class="form-control input-sm" id="dimension_z" value="{{ old('dimension_z', $car->dimension_z ?? 0) }}" required >--}}
{{--        {!! $errors->first('dimension_z', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}

<hr>

<div class="form-group row ">
    <div class="offset-md-3 col-md-6">
        <h5>Водительское удостоверение</h5>
    </div>
</div>

<div class="form-group row {!! $errors->first('licence_number', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="licence_number">Номер </label>
    <div class="col-md-4">
        <input type="text" name="licence_number" class="form-control input-sm" id="licence_number" value="{{ old('licence_number', $user->profile->licence_number ?? '') }}" >
        {!! $errors->first('licence_number', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('licence', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="licence">Прикрепить файл</label>
    <div class="col-md-4">
        <input type="file" name="licence" class="form-control input-sm " id="licence"  >
        {!! $errors->first('licence', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<hr>
<div class="form-group row ">
    <div class="offset-md-3 col-md-6">
        <h5>Tех. паспорта</h5>
    </div>
</div>
<div class="form-group row {!! $errors->first('car_licence_number', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="car_licence_number">Номер </label>
    <div class="col-md-4">
        <input type="text" name="car_licence_number" class="form-control input-sm" id="car_licence_number" value="{{ old('car_licence_number', $user->profile->car_licence_number ?? '') }}" >
        {!! $errors->first('car_licence_number', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('car_licence', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="car_licence">Прикрепить файл</label>
    <div class="col-md-4">
        <input type="file" name="car_licence" class="form-control input-sm " id="car_licence"  >
        {!! $errors->first('car_licence', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<hr>

<div class="form-group row ">
    <div class="offset-md-3 col-md-6">
        <h5>@lang('validation.attributes.supported_cargo_types')</h5>
    </div>
</div>

<div class="form-group row {!! $errors->first('cargo_types', 'has-danger')!!} ">
    <div class="col-md-6 offset-md-3">
        <div class="row">
            <div class="col-md-6 mt-2">
                <div class="icheck-primary d-inline">
                    <input type="checkbox"  id="cargo_type_check_all" value="">
                    <label class="text-secondary" for="cargo_type_check_all">
                        Выбрать все типы грузов
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
        @foreach(\App\Domain\CargoTypes\Models\CargoType::withTranslation()->get() as $i => $item)
            <div class="col-md-6 mt-2">
                <div class="icheck-primary d-inline">
                    <input type="checkbox"  class="cargo_type_checkbox" name="cargo_types[]" id="checkboxPrimary{{$i}}" value="{{ $item->id }}" {{ (in_array($item->id, array_keys(isset($car) ? $car->cargoTypes->keyBy('id')->toArray(): [])))? 'checked="checked"' : '' }}>
                    <label class="text-secondary" for="checkboxPrimary{{$i}}">
                        {{ $item->title }}
                    </label>
                </div>
            </div>
        @endforeach
        {!! $errors->first('cargo_types', '<small class="form-control-feedback">:message</small>') !!}
        </div>
    </div>
</div>

<div class="form-group row">
    <label class="col-md-3 text-md-right col-form-label-sm" for="active">@lang('validation.attributes.active')</label>
    <div class="col-md-4">
        <div class="btn-group btn-group-sm" data-toggle="buttons">
            <label class="btn btn-outline-success {{ old('active', $car->active ?? 1) == 1? 'active': '' }}">
                <input type="radio" name="active" value="1" {{ old('active', $car->active ?? 1) == 1? 'checked': '' }} required>
                @lang('admin.active')
            </label>
            <label class="btn btn-outline-danger {{ old('active', $car->active ?? 1) == 0? 'active': '' }}">
                <input type="radio" name="active" value="0" {{ old('active', $car->active ?? 1) == 0? 'checked': '' }} required>
                @lang('admin.not_active')
            </label>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(function () {
            $('#cargo_type_check_all').prop('checked', checkCargoTypes());

            $('#cargo_type_check_all').on('change', function() {
                if($(this).is(':checked')) {
                    $('.cargo_type_checkbox').each(function () {
                        $(this).prop('checked', true);
                    });
                } else {
                    $('.cargo_type_checkbox').each(function () {
                        $(this).prop('checked', false);
                    });
                }
            });

            function checkCargoTypes()
            {
                var checked = true;
                $('.cargo_type_checkbox').each(function () {
                    if(!$(this).is(':checked')) {
                        checked = false;
                    }
                });
                return checked;
            }
        });
    </script>
@endpush

@push('scripts')
    <script type="text/javascript"
            src="{{ asset('vendor/inputmask/jquery.inputmask.bundle.js') }}"></script>
    <script>
        $(function () {
            $('input[name="birthday"]').inputmask("99/99/9999");
        });
    </script>
@endpush
