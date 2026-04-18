<div class="form-group row {!! $errors->first('name', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="name">@lang('validation.attributes.title')</label>
    <div class="col-md-6">
        <input type="text"  name="name" class="form-control input-sm" id="name" value="{{ old('name', $priority->name ?? '') }}" required >
        {!! $errors->first('title', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<div class="form-group row {!! $errors->first('quantity', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="quantity">Лимит долга</label>
    <div class="col-md-6">
        <input type="number" min="1" name="quantity" class="form-control input-sm" id="quantity" value="{{ old('quantity', $priority->quantity ?? 0) }}" required >
        {!! $errors->first('quantity', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
