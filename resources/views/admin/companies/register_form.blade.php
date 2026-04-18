<h5 >Аккаунт</h5>

<div class="row">
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('username', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="username">Номер телефона / Логин</label>
            <div class="">
                <input type="text" name="username" class="form-control input-sm" id="username" {{ isset($user) ? 'readonly': '' }} value="{{ old('username', $user->username ?? '') }}" required>
                {!! $errors->first('username', '<small class="form-control-feedback">:message</small>') !!}
            </div>
            <div class="small text-muted">Без плюса в формате 998XX0000000</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('password', 'has-danger')!!}" >
            <label class=" text-md-right text-secondary" for="password">@lang('validation.attributes.password')</label>
            <div class="">
                <input type="password" name="password" class="form-control input-sm password" id="password" {{ isset($user->id) ? '': 'required' }}>
                {!! $errors->first('password', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
</div>

<hr class="">
<h5>Руководитель</h5>
<div class="row">
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('name', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="name">Имя</label>
            <div class="">
                <input type="text" name="name" class="form-control input-sm" id="name" value="{{ old('name', $user->profile->name ?? '') }}" required >
                {!! $errors->first('name', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('surname', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="surname">Фамилия</label>
            <div class="">
                <input type="text" name="surname" class="form-control input-sm" id="surname" value="{{ old('surname', $user->profile->surname ?? '') }}" >
                {!! $errors->first('surname', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">

        <div class="form-group  {!! $errors->first('middle_name', 'has-danger')!!}">
            <label class=" text-md-right col-form-label-sm" for="middle_name">Отчество</label>
            <div class="">
                <input type="text" name="middle_name" class="form-control input-sm" id="middle_name" value="{{ old('middle_name', $user->profile->middle_name ?? '') }}" >
                {!! $errors->first('middle_name', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>

    </div>
</div>
<hr class="">
<h5>Информация о компании</h5>
<div class="row">
    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('title', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="title">Название компании</label>
            <div class="">
                <input type="text" name="title" class="form-control input-sm" id="title"
                       value="{{ old('title', $company->title ?? '') }}" required >
                {!! $errors->first('title', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('logo', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="logo">Логотип</label>
            <div class="">
                <input type="file" name="logo" class="form-control input-sm " id="logo"  >
                {!! $errors->first('logo', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>

    <div class="col-sm-3">

        <div class="form-group  {!! $errors->first('company_address', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="company_address">Юридический адрес</label>
            <div class="">
                <input type="text" name="company_address" class="form-control input-sm " id="company_address" value="{{ old('company_address', $company->company_address ?? '') }}" >
                {!! $errors->first('company_address', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('address', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="address">Фактический адрес компании</label>
            <div class="">
                <input type="text" name="address" class="form-control input-sm" id="address"
                       value="{{ old('address', $company->address ?? '') }}" >
                {!! $errors->first('address', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
</div>
<div class="row">
</div>

<div class="border mt-3 mb-3"></div>

<h5>Документы компании</h5>

<div class="row">
    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('certificate', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="certificate">Гувохнома</label>
            <div class="">
                <input type="file" name="certificate" class="form-control input-sm " id="certificate"  >
                {!! $errors->first('certificate', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('licence', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="licence">Лицензия</label>
            <div class="">
                <input type="file" name="licence" class="form-control input-sm " id="licence"  >
                {!! $errors->first('licence', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('agreement', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="agreement">Договор о сотрудничестве</label>
            <div class="">
                <input type="file" name="agreement" class="form-control input-sm " id="agreement"  >
                {!! $errors->first('agreement', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
</div>



<div class="border mt-3 mb-3"></div>

<h5>Банковские реквизиты</h5>

<div class="row">
    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('bank_account', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="bank_account">@lang('admin.bank_account')</label>
            <div class="">
                <input type="text" name="bank_account" class="form-control input-sm bank_account" id="bank_account" value="{{ old('bank_account', $company->bank_account ?? '') }}" >
                {!! $errors->first('bank_account', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">

        <div class="form-group  {!! $errors->first('bank', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="bank">@lang('admin.bank')</label>
            <div class="">
                <input type="text" name="bank" class="form-control input-sm bank" id="bank" value="{{ old('bank', $company->bank ?? '') }}" >
                {!! $errors->first('bank', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
</div>

<div class="border mt-3 mb-3"></div>

<h5>Юридические данные</h5>

<div class="row">
    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('inn', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="inn">@lang('admin.inn')</label>
            <div class="">
                <input type="text" name="inn" class="form-control input-sm inn" id="inn" value="{{ old('inn', $company->inn ?? '') }}" >
                {!! $errors->first('inn', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">

        <div class="form-group  {!! $errors->first('mfo', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="mfo">@lang('admin.mfo')</label>
            <div class="">
                <input type="text" name="mfo" class="form-control input-sm mfo" id="mfo" value="{{ old('mfo', $company->mfo ?? '') }}" >
                {!! $errors->first('mfo', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">

        <div class="form-group  {!! $errors->first('oked', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="oked">@lang('admin.oked')</label>
            <div class="">
                <input type="text" name="oked" class="form-control input-sm oked" id="oked" value="{{ old('oked', $company->oked ?? '') }}" >
                {!! $errors->first('oked', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>

    </div>
</div>


<div class="border mt-3 mb-3"></div>

<div class="row">

    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('telegram', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="telegram">Telegram </label>
            <div class="">
                <input type="text" name="telegram" class="form-control input-sm" id="telegram" value="{{ old('telegram', $user->profile->telegram ?? '') }}" >
                {!! $errors->first('telegram', '<small class="form-control-feedback">:message</small>') !!}
            </div>
            <div class="small text-muted">Username/Номер/Ссылка на Telegram </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('email', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="email">@lang('admin.emails')</label>
            <div class="">
                <input type="email" name="email" class="form-control input-sm" id="email" value="{{ old('email', $user->email ?? '') }}"  >
                {!! $errors->first('email', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
</div>

<div class="border mt-3 mb-3"></div>
{{--<div class="form-group row ">--}}
{{--    <div class="offset-md-3 col-md-6">--}}
{{--        <h5>@lang('admin.requisites')</h5>--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="form-group row {!! $errors->first('company_name', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right text-secondary" for="company_name">@lang('admin.company_name')</label>--}}
{{--    <div class="col-md-6">--}}
{{--        <input type="text" name="company_name" class="form-control input-sm company_name" id="company_name" value="{{ old('company_name', $company->company_name ?? '') }}" >--}}
{{--        {!! $errors->first('company_name', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="form-group row {!! $errors->first('company_city', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right text-secondary" for="company_city">@lang('admin.company_city')</label>--}}
{{--    <div class="col-md-6">--}}
{{--        <input type="text" name="company_city" class="form-control input-sm company_city" id="company_city" value="{{ old('company_city', $company->company_city ?? '') }}" >--}}
{{--        {!! $errors->first('company_city', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}


{{--<div class="form-group row {!! $errors->first('post_index', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right text-secondary" for="post_index">@lang('admin.post_index')</label>--}}
{{--    <div class="col-md-6">--}}
{{--        <input type="text" name="post_index" class="form-control input-sm post_index" id="post_index" value="{{ old('post_index', $company->post_index ?? '') }}" >--}}
{{--        {!! $errors->first('post_index', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}




{{--<div class="form-group row {!! $errors->first('okonh', 'has-danger')!!}">--}}
{{--    <label class="col-md-3 text-md-right text-secondary" for="okonh">@lang('admin.okonh')</label>--}}
{{--    <div class="col-md-6">--}}
{{--        <input type="text" name="okonh" class="form-control input-sm okonh" id="okonh" value="{{ old('okonh', $company->okonh ?? '') }}" >--}}
{{--        {!! $errors->first('okonh', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--    </div>--}}
{{--</div>--}}


{{--<div class="form-group row {!! $errors->first('document', 'has-danger')!!}">--}}
{{--<label class="col-md-3 text-md-right text-secondary" for=document>@lang('admin.document')</label>--}}
{{--<div class="col-md-9">--}}
{{--<textarea name="document" class="form-control text-editor " id=document >{{ old('document', $company->document ?? '') }}</textarea>--}}
{{--{!! $errors->first('document', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--</div>--}}
{{--</div>--}}
{{--@component('component.tiny-mc')@endcomponent--}}


@component('component.phone-stacks')@endcomponent
@component('component.add-remove-element-stacks', ['min' => 1, 'element' => 'input-group', 'wrapperId' => 'phones', 'removeClass' => 'remove_phone', 'addClass' => 'add_phone', 'inputmask' => '+\\\9\\\9\\\8 (99) 999-99-99'])@endcomponent
@component('component.add-remove-element-stacks', ['min' => 1, 'element' => 'input-group', 'wrapperId' => 'emails', 'removeClass' => 'remove_email', 'addClass' => 'add_email'])@endcomponent

