@component('component.translations', ['form' => 'admin.car-types._translations_form', 'model' => $carType?? null])@endcomponent
<div class="form-group row {!! $errors->first('priority', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="priority">@lang('validation.attributes.priority')</label>
    <div class="col-md-6">
        <input type="number" step="1" min="0" name="priority" class="form-control input-sm" id="priority" value="{{ old('priority', $carType->priority ?? 0) }}"  >
        {!! $errors->first('priority', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<div class="form-group row {!! $errors->first('pickup_limit', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="pickup_limit">Беспалтное время ожидания погрузки </label>
    <div class="col-md-6">
        <div class="input-group">
            <input type="number" step="1" min="0" name="pickup_limit" class="form-control input-sm" id="pickup_limit" value="{{ old('pickup_limit', $carType->pickup_limit ?? 0) }}"  >
            <span class="input-group-text">
                минут
            </span>
        </div>
        {!! $errors->first('pickup_limit', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<div class="form-group row {!! $errors->first('pickup_per_minute', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="pickup_per_minute">Цена за минуту ожидания погрузки </label>
    <div class="col-md-6">
        <div class="input-group">
            <input type="number" step="1" min="0" name="pickup_per_minute" class="form-control input-sm" id="pickup_per_minute" value="{{ old('pickup_per_minute', $carType->pickup_per_minute ?? 0) }}"  >
            <span class="input-group-text">
                сум
            </span>
        </div>
        {!! $errors->first('pickup_per_minute', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>



<div class="form-group row {!! $errors->first('unloading_limit', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="unloading_limit">Беспалтное время ожидания разгрузку </label>
    <div class="col-md-6">
        <div class="input-group">
            <input type="number" step="1" min="0" name="unloading_limit" class="form-control input-sm" id="unloading_limit" value="{{ old('unloading_limit', $carType->unloading_limit ?? 0) }}"  >
            <span class="input-group-text">
                минут
            </span>
        </div>
        {!! $errors->first('unloading_limit', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<div class="form-group row {!! $errors->first('unloading_per_minute', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="unloading_per_minute">Цена за минуту ожидания разгрузки </label>
    <div class="col-md-6">
        <div class="input-group">
            <input type="number" step="1" min="0" name="unloading_per_minute" class="form-control input-sm" id="unloading_per_minute" value="{{ old('unloading_per_minute', $carType->unloading_per_minute ?? 0) }}"  >
            <span class="input-group-text">
                сум
            </span>
        </div>
        {!! $errors->first('unloading_per_minute', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<div class="form-group row {!! $errors->first('commission', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="commission">@lang('validation.attributes.commission') <small class="text-muted">(в %)</small></label>
    <div class="col-md-6">
        <input type="number" step="0.01" min="0" name="commission" class="form-control input-sm" id="commission" value="{{ old('commission', $carType->commission ?? 0) }}"  >
        {!! $errors->first('commission', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('commission', 'has-danger')!!}">
        <label class="col-md-3 text-md-right col-form-label-sm" for="active">Междугородний транспорт</label>
        <div class="col-md-4">
            <div class="btn-group btn-group-sm" data-toggle="buttons">
                <label class="btn btn-outline-success {{ old('is_multi_region', $carType->is_multi_region ?? 1) == 1? 'active': '' }}">
                    <input type="radio" name="is_multi_region" value="1" {{ old('is_multi_region', $carType->is_multi_region ?? 1) == 1? 'checked': '' }} required>
                    @lang('admin.yes')
                </label>
                <label class="btn btn-outline-danger {{ old('active', $user->active ?? 1) == 0? 'active': '' }}">
                    <input type="radio" name="is_multi_region" value="0" {{ old('is_multi_region', $carType->is_multi_region ?? 1) == 0? 'checked': '' }} required>
                    @lang('admin.no')
                </label>
            </div>
        </div>
</div>

<div class="form-group row ">
    <div class="offset-md-3 col-md-6">
        <h5>@lang('validation.attributes.dimensions')</h5>
    </div>
</div>

<div class="form-group row {!! $errors->first('max_weight', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="max_weight">@lang('validation.attributes.max_weight') <small class="text-muted">(кг)</small></label>
    <div class="col-md-6">
        <input type="number" step="1" min="0" name="max_weight" class="form-control input-sm" id="max_weight" value="{{ old('max_weight', $carType->max_weight ?? 0) }}"  >
        {!! $errors->first('max_weight', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('dimension_x', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="dimension_x">@lang('validation.attributes.dimension_x') <small class="text-muted">(м)</small></label>
    <div class="col-md-6">
        <input type="number" step="0.01" min="0" name="dimension_x" class="form-control input-sm" id="dimension_x" value="{{ old('dimension_x', $carType->dimension_x ?? 0) }}"  >
        {!! $errors->first('dimension_x', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<div class="form-group row {!! $errors->first('dimension_y', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="dimension_y">@lang('validation.attributes.dimension_y') <small class="text-muted">(м)</small></label>
    <div class="col-md-6">
        <input type="number" step="0.01" min="0" name="dimension_y" class="form-control input-sm" id="dimension_y" value="{{ old('dimension_y', $carType->dimension_y ?? 0) }}"  >
        {!! $errors->first('dimension_y', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<div class="form-group row {!! $errors->first('dimension_z', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="dimension_z">@lang('validation.attributes.dimension_z') <small class="text-muted">(м)</small></label>
    <div class="col-md-6">
        <input type="number" step="0.01" min="0" name="dimension_z" class="form-control input-sm" id="dimension_z" value="{{ old('dimension_z', $carType->dimension_z ?? 0) }}"  >
        {!! $errors->first('dimension_z', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('icon', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="icon">@lang('validation.attributes.icon')</label>
    <div class="col-md-6">
        <input type="file" name="icon" class="form-control input-sm" id="icon"  >
        {!! $errors->first('icon', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
