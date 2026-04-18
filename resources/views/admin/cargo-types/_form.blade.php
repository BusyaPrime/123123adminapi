@component('component.translations', ['form' => 'admin.cargo-types._translations_form', 'model' => $cargoType?? null])@endcomponent
<div class="form-group row {!! $errors->first('priority', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="priority">@lang('validation.attributes.priority')</label>
    <div class="col-md-6">
        <input type="number" step="1" min="0" name="priority" class="form-control input-sm" id="priority" value="{{ old('priority', $cargoType->priority ?? 0) }}"  >
        {!! $errors->first('priority', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
