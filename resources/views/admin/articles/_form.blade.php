@component('component.translations', ['form' => 'admin.articles._translations_form', 'model' => $item?? null])@endcomponent

<div class="form-group row {!! $errors->first('image', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="image">Фото</label>
    <div class="col-md-6">
        <input type="file" name="image" class="form-control input-sm" id="image"  >
        {!! $errors->first('image', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
