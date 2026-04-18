<div class="form-group row {!! $errors->first('reason', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="reason">Причина</label>
    <div class="col-md-6">
        <?php $value = old('translations.'.$lang.'.reason', (isset($model) && $model->translate($lang)) ? $model->translate($lang)->reason: '') ?>
        <input type="text"   name="translations[{{$lang}}][reason]" class="form-control input-sm" id="reason_{{$lang}}" value="{{ $value }}" {{ ($lang == LaravelLocalization::getDefaultLocale())? 'required autofocus': '' >
        {!! $errors->first('reason', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>