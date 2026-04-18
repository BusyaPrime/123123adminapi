<div class="form-group row {!! $errors->first('title', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="title">@lang('validation.attributes.title')</label>
    <div class="col-md-6">
        <input type="text"  name="title" class="form-control input-sm" id="title" value="{{ old('title', $rate->title ?? '') }}" required >
        {!! $errors->first('title', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<div class="form-group row {!! $errors->first('commission', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="commission">Комиссия</label>
    <div class="col-md-6">
        <input type="number" step="1" min="0" max="100" name="commission" class="form-control input-sm" id="commission" value="{{ old('commission', $rate->commission ?? 0) }}" required >
        {!! $errors->first('commission', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
