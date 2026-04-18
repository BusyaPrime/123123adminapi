@component('component.translations', ['form' => 'admin.sizes._translations_form', 'model' => $size?? null])@endcomponent
<div class="form-group row {!! $errors->first('priority', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="priority">@lang('validation.attributes.priority')</label>
    <div class="col-md-6">
        <input type="number" step="1" min="0" name="priority" class="form-control input-sm" id="priority" value="{{ old('priority', $size->priority ?? 0) }}"  >
        {!! $errors->first('priority', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row ">
    <div class="offset-md-3 col-md-6">
        <h5>@lang('validation.attributes.dimensions')</h5>
    </div>
</div>

<div class="form-group row {!! $errors->first('dimension_x', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="dimension_x">@lang('validation.attributes.dimension_x') <small class="text-muted">(м)</small></label>
    <div class="col-md-6">
        <input type="number" step="0.01" min="0" name="dimension_x" class="form-control input-sm" id="dimension_x" value="{{ old('dimension_x', $size->dimension_x ?? 0) }}"  >
        {!! $errors->first('dimension_x', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<div class="form-group row {!! $errors->first('dimension_y', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="dimension_y">@lang('validation.attributes.dimension_y') <small class="text-muted">(м)</small></label>
    <div class="col-md-6">
        <input type="number" step="0.01" min="0" name="dimension_y" class="form-control input-sm" id="dimension_y" value="{{ old('dimension_y', $size->dimension_y ?? 0) }}"  >
        {!! $errors->first('dimension_y', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<div class="form-group row {!! $errors->first('dimension_z', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="dimension_z">@lang('validation.attributes.dimension_z') <small class="text-muted">(м)</small></label>
    <div class="col-md-6">
        <input type="number" step="0.01" min="0" name="dimension_z" class="form-control input-sm" id="dimension_z" value="{{ old('dimension_z', $size->dimension_z ?? 0) }}"  >
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
