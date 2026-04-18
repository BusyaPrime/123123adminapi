<div class="form-group row {!! $errors->first('name', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="name">Имя</label>
    <div class="col-md-4">
        <input type="text" name="name" class="form-control input-sm" id="name" value="{{ old('name', $user->profile->name ?? '') }}" required autofocus>
        {!! $errors->first('name', '<small style="color:red;" class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<div class="form-group row {!! $errors->first('surname', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="surname">Фамилия</label>
    <div class="col-md-4">
        <input type="text" name="surname" class="form-control input-sm" id="surname" value="{{ old('surname', $user->profile->surname ?? '') }}" >
        {!! $errors->first('surname', '<small style="color:red;" class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<div class="form-group row {!! $errors->first('middle_name', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="middle_name">Отчество</label>
    <div class="col-md-4">
        <input type="text" name="middle_name" class="form-control input-sm" id="middle_name" value="{{ old('middle_name', $user->profile->middle_name ?? '') }}" >
        {!! $errors->first('middle_name', '<small style="color:red;" class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('birthday', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="birthday">Дата рождения</label>
    <div class="col-md-4">
        <input type="text" name="birthday" class="form-control input-sm" id="birthday" value="{{ old('birthday', $user->profile->birthday ?? '') }}" >
        {!! $errors->first('birthday', '<small style="color:red;" class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<hr>
<div class="form-group row {!! $errors->first('username', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="username">@lang('validation.attributes.username') </label>
    <div class="col-md-4">
        <input type="text" name="username" class="form-control input-sm" id="username" {{ isset($user) ? 'readonly': '' }} value="{{ old('username', $user->username ?? '') }}" required>
        {!! $errors->first('username', '<small style="color:red;" class="form-control-feedback">:message</small>') !!}
    </div>
    <div class="col-md-5">Номер телефона без плюса в формате 998XX0000000</div>
</div>
<div class="form-group row {!! $errors->first('telegram', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="telegram">Telegram </label>
    <div class="col-md-4">
        <input type="text" name="telegram" class="form-control input-sm" id="telegram" value="{{ old('telegram', $user->profile->telegram ?? '') }}" >
        {!! $errors->first('telegram', '<small style="color:red;" class="form-control-feedback">:message</small>') !!}
    </div>
    <div class="col-md-5">Username/Номер/Ссылка на Telegram поль-ля</div>
</div>
<div class="form-group row {!! $errors->first('email', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="email">Эл. почта </label>
    <div class="col-md-4">
        <input type="email" name="email" class="form-control input-sm" id="email" value="{{ old('email', $user->email ?? '') }}" >
        {!! $errors->first('email', '<small style="color:red;" class="form-control-feedback">:message</small>') !!}
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
        {!! $errors->first('region_id', '<small style="color:red;" class="form-control-feedback">:message</small>') !!}
    </div>
</div>

{{--<div class="form-group row {!! $errors->first('balance', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="balance">@lang('validation.attributes.balance') <small class="text-muted">(сум)</small></label>--}}
{{--    <div class="col-md-4">--}}
{{--        <input type="number" step="1" min="0" name="balance" class="form-control input-sm" id="balance" value="{{ old('balance', $user->profile->balance ?? 0) }}" required>--}}
{{--        {!! $errors->first('balance', '<small style="color:red;" class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}
<div class="form-group row">
    <label class="col-md-3 text-md-right col-form-label-sm" for="active">@lang('validation.attributes.active')</label>
    <div class="col-md-4">
        <div class="btn-group btn-group-sm" data-toggle="buttons">
            <label class="btn btn-outline-success {{ old('active', $user->active ?? 1) == 1? 'active': '' }}">
                <input type="radio" name="active" value="1" {{ old('active', $user->active ?? 1) == 1? 'checked': '' }} required>
                @lang('admin.active')
            </label>
            <label class="btn btn-outline-danger {{ old('active', $user->active ?? 1) == 0? 'active': '' }}">
                <input type="radio" name="active" value="0" {{ old('active', $user->active ?? 1) == 0? 'checked': '' }} required>
                @lang('admin.not_active')
            </label>
        </div>
    </div>
</div>
{{--<div class="form-group row">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="can_call">@lang('validation.attributes.can_call')</label>--}}
{{--    <div class="col-md-4">--}}
{{--        <div class="btn-group btn-group-sm" data-toggle="buttons">--}}
{{--            <label class="btn btn-outline-success {{ old('can_call', $user->profile->can_call ?? 1) == 1? 'active': '' }}">--}}
{{--                <input type="radio" name="can_call" value="1" {{ old('can_call', $user->profile->can_call ?? 1) == 1? 'checked': '' }} >--}}
{{--                @lang('admin.yes')--}}
{{--            </label>--}}
{{--            <label class="btn btn-outline-danger {{ old('can_call', $user->profile->can_call ?? 1) == 0? 'active': '' }}">--}}
{{--                <input type="radio" name="can_call" value="0" {{ old('can_call', $user->profile->can_call ?? 1) == 0? 'checked': '' }} >--}}
{{--                @lang('admin.no')--}}
{{--            </label>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="form-group row">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="have_terminal">@lang('validation.attributes.have_terminal')</label>--}}
{{--    <div class="col-md-4">--}}
{{--        <div class="btn-group btn-group-sm" data-toggle="buttons">--}}
{{--            <label class="btn btn-outline-success {{ old('have_terminal', $user->profile->have_terminal ?? 0) == 1? 'active': '' }}">--}}
{{--                <input type="radio" name="have_terminal" value="1" {{ old('have_terminal', $user->profile->have_terminal ?? 1) == 1? 'checked': '' }} >--}}
{{--                @lang('admin.yes')--}}
{{--            </label>--}}
{{--            <label class="btn btn-outline-danger {{ old('have_terminal', $user->profile->have_terminal ?? 0) == 0? 'active': '' }}">--}}
{{--                <input type="radio" name="have_terminal" value="0" {{ old('have_terminal', $user->profile->have_terminal ?? 1) == 0? 'checked': '' }} >--}}
{{--                @lang('admin.no')--}}
{{--            </label>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="form-group row">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="lang">@lang('validation.attributes.lang')</label>--}}
{{--    <div class="col-md-4">--}}
{{--        <div class="btn-group btn-group-sm" data-toggle="buttons">--}}
{{--            <label class="btn btn-outline-success {{ old('lang', $user->profile->language ?? 'ru') == 'ru'? 'active': '' }}">--}}
{{--                <input type="radio" name="lang" value="ru" {{ old('lang', $user->profile->language ?? 'ru') == 'ru'? 'checked': '' }} >--}}
{{--                @lang('admin.locales.ru')--}}
{{--            </label>--}}
{{--            <label class="btn btn-outline-success {{ old('lang', $user->profile->language ?? 'ru') == 'en'? 'active': '' }}">--}}
{{--                <input type="radio" name="lang" value="en" {{ old('lang', $user->profile->language ?? 'ru') == 'en'? 'checked': '' }} >--}}
{{--                @lang('admin.locales.en')--}}
{{--            </label>--}}
{{--            <label class="btn btn-outline-success {{ old('lang', $user->profile->language ?? 'ru') == 'uz'? 'active': '' }}">--}}
{{--                <input type="radio" name="lang" value="uz" {{ old('lang', $user->profile->language ?? 'ru') == 'uz'? 'checked': '' }} >--}}
{{--                @lang('admin.locales.uz')--}}
{{--            </label>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="form-group row {!! $errors->first('licence_number', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="licence_number">@lang('validation.attributes.licence_number')</label>--}}
{{--    <div class="col-md-4">--}}
{{--        <input type="text" name="licence_number" class="form-control input-sm" id="licence_number" value="{{ old('licence_number', $user->profile->licence_number ?? '') }}" >--}}
{{--        {!! $errors->first('licence_number', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="form-group row {!! $errors->first('licence_expired_at', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="licence_expired_at">@lang('validation.attributes.licence_expired_at')</label>--}}
{{--    <div class="col-md-4">--}}
{{--        <input type="date" name="licence_expired_at" class="form-control input-sm" id="licence_expired_at" value="{{ old('licence_expired_at', $user->profile->licence_expired_at ?? '') }}" >--}}
{{--        {!! $errors->first('licence_expired_at', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="form-group row {!! $errors->first('licence', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right col-form-label-sm" for="licence">@lang('validation.attributes.licence')</label>--}}
{{--    <div class="col-md-4">--}}
{{--        <input type="file" name="licence" class="form-control input-sm" id="licence"  >--}}
{{--        {!! $errors->first('licence', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--    <div class="col-md-3">--}}
{{--        @if(isset($user) && isset($user->profile) && $user->profile->licence)--}}
{{--            <a href="{{ $user->profile->licenceUrl() }}">Просмотр лицензии</a>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}
@push('scripts')
    <script type="text/javascript"
            src="{{ asset('vendor/inputmask/jquery.inputmask.bundle.js') }}"></script>
    <script>
        $(function () {
            $('input[name="birthday"]').inputmask("99/99/9999");
            $('input[name="username"]').inputmask("+\\9\\9\\8 \\(99\\) 999\\-99\\-99");
        });
    </script>
@endpush
