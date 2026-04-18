<?php $value = old('translations.'.$lang.'.title', (isset($model) && $model->translate($lang)) ? $model->translate($lang)->title: '') ?>
<div class="form-group row {!! $errors->first('translations.'.$lang.'.title', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="{{$lang}}-title">Название</label>
    <div class="col-md-4">
        <input type="text" name="translations[{{ $lang }}][title]" class="form-control input-sm" id="{{$lang}}-title" value="{{ $value }}" {{ ($lang == LaravelLocalization::getDefaultLocale())? 'required autofocus': '' }} >
        {!! $errors->first('translations.'.$lang.'.title', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<?php $value = old('translations.'.$lang.'.short', (isset($model) && $model->translate($lang)) ? $model->translate($lang)->short: '') ?>
<div class="form-group row {!! $errors->first('translations.'.$lang.'.short', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="{{$lang}}-short">Анонс</label>
    <div class="col-md-4">
        <textarea name="translations[{{ $lang }}][short]" id="{{$lang}}-short" class="form-control " rows="4" >{{$value}}</textarea>
        {!! $errors->first('translations.'.$lang.'.short', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<?php $value = old('translations.'.$lang.'.full', (isset($model) && $model->translate($lang)) ? $model->translate($lang)->full: '') ?>
<div class="form-group row {!! $errors->first('translations.'.$lang.'.full', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="{{$lang}}-full">Полный текст</label>
    <div class="col-md-4">
        <textarea name="translations[{{ $lang }}][full]" id="{{$lang}}-full" class="form-control " rows="7" >{{$value}}</textarea>
        {!! $errors->first('translations.'.$lang.'.full', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<?php $value = old('translations.'.$lang.'.link', (isset($model) && $model->translate($lang)) ? $model->translate($lang)->link: '') ?>
<div class="form-group row {!! $errors->first('translations.'.$lang.'.link', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="{{$lang}}-link">Ссылка</label>
    <div class="col-md-4">
        <input type="text" name="translations[{{ $lang }}][link]" class="form-control input-sm" id="{{$lang}}-link" value="{{ $value }}" {{ ($lang == LaravelLocalization::getDefaultLocale())? '': '' }} >
        {!! $errors->first('translations.'.$lang.'.link', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
