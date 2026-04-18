<?php $value = old('translations.'.$lang.'.title', (isset($model) && $model->translate($lang)) ? $model->translate($lang)->title: '') ?>
<div class="form-group row {!! $errors->first('translations.'.$lang.'.title', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="{{$lang}}-title">@lang('validation.attributes.title')</label>
    <div class="col-md-4">
        <input type="text" name="translations[{{ $lang }}][title]" class="form-control input-sm" id="{{$lang}}-title" value="{{ $value }}" {{ ($lang == LaravelLocalization::getDefaultLocale())? 'required autofocus': '' }} >
        {!! $errors->first('translations.'.$lang.'.title', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
