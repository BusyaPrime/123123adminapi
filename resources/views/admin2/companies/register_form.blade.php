<style>
    .danger-text{
    color:red!important;
    font-size:15px;
}
</style>
<h5 >Аккаунт *</h5>

<input type="hidden" value='{{ App\Domain\Companies\Models\Company::ROLE_COMPANY }}' name='companyRole' />

<div class="row">
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('username', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="username">Логин (только символы латинского альфавита) <span class="danger-text">*</span></label>
            <div class="input-group mb-2">
                <input type="text" name="username" class="form-control input-sm d-block login-input" id="username" value="" required placeholder="Логин" >
                {!! $errors->first('username', '<small class="form-control-feedback">:message</small>') !!}
            </div>
            <!--<div class="small text-muted">Без плюса в формате 998XX0000000</div>-->
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('password', 'has-danger')!!}" >
            <label class=" text-md-right text-secondary" for="password">@lang('validation.attributes.password') <span class="danger-text">*</span></label>
            <div class="">
                <input type="password" name="password" class="form-control input-sm password" id="password" {{ isset($user->id) ? '': 'required' }}>
                {!! $errors->first('password', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
	
	<div class="col-md-3">
        <div class="form-group  {!! $errors->first('confirmpassword', 'has-danger')!!}" >
            <label class=" text-md-right text-secondary" for="confirmpassword">Подтверждение пароля <span class="danger-text">*</span></label>
            <div class="">
                <input type="password" name="confirmpassword" class="form-control input-sm password" id="confirmpassword" {{ isset($user->id) ? '': 'required' }}>
                {!! $errors->first('confirmpassword', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
</div>

<hr class="">
<h5>Руководитель </h5>
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
            <label class=" text-md-right col-form-label-sm p-0" for="middle_name">Отчество</label>
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
            <label class=" text-md-right col-form-label-sm p-0" for="title">Название компании <span class="danger-text">*</span></label>
            <div class="">
                <input type="text" name="title" class="form-control input-sm" id="title"
                       value="{{ old('title', $company->title ?? '') }}" required >
                {!! $errors->first('title', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('inn', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="inn">@lang('admin.inn') <span class="danger-text">*</span></label>
            <div class="">
                <input type="text" name="inn" required class="form-control input-sm inn" id="inn" value="{{ old('inn', $company->inn ?? '') }}" >
                {!! $errors->first('inn', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>

    <!-- <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('logo', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="logo">Логотип</label>
            <div class="custom-file">
                <input type="file" name="logo" class="form-control input-sm custom-file-input " id="logo" accept=".jpg,.jpeg,.png,.doc,.pdf" >
				<label class="custom-file-label logolabel" for="logo"><span>Файл не выбран</span></label>
                {!! $errors->first('logo', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div> -->

    <div class="col-sm-3">

        <div class="form-group  {!! $errors->first('company_address', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="company_address">Юридический адрес</label>
            <!-- <div class="extra_height"> -->
                <input type="text" name="company_address" class="form-control input-sm " id="company_address" value="{{ old('company_address', $company->company_address ?? '') }}" >
                {!! $errors->first('company_address', '<small class="form-control-feedback">:message</small>') !!}
            <!-- </div> -->
        </div>
    </div>

</div>
<div class="row">
</div>

<div class="border mt-3 mb-3"></div>

<div class="border mt-3 mb-3"></div>

<h5>Банковские реквизиты</h5>

<div class="row">

    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('bank', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="bank">@lang('admin.bank') <span class="danger-text">*</span></label>
            <div class="">
                <input type="text" name="bank" class="form-control input-sm bank" required id="bank" value="{{ old('bank', $company->bank ?? '') }}" >
                {!! $errors->first('bank', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('bank_account', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="bank_account">@lang('admin.bank_account') <span class="danger-text">*</span></label>
            <div class="">
                <input type="text" required name="bank_account" class="form-control input-sm bank_account" id="bank_account" value="{{ old('bank_account', $company->bank_account ?? '') }}" >
                {!! $errors->first('bank_account', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>

    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('mfo', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="mfo">@lang('admin.mfo') <span class="danger-text">*</span></label>
            <div class="">
                <input type="text" name="mfo" required class="form-control input-sm mfo" id="mfo" value="{{ old('mfo', $company->mfo ?? '') }}" >
                {!! $errors->first('mfo', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>

</div>

<div class="border mt-3 mb-3"></div>

<h5>Юридические данные</h5>

<div class="row">
</div>


<div class="border mt-3 mb-3"></div>

<h5>Дополнительная информация</h5>

<div class="row">
	<div class="col-md-3">
        <div class="form-group  {!! $errors->first('phonenumber', 'has-danger')!!}">
            <label class="text-md-right text-secondary" for="phonenumber">Номер телефона <span class="danger-text">*</span></label>
            <div class="">
                <input type="text" name="phonenumber" required class="phone-input form-control input-sm" id="phonenumber" {{ isset($user) ? 'readonly': '' }} value="{{ old('phonenumber', $user->phonenumber ?? '') }}" required>
                {!! $errors->first('phonenumber', '<small class="form-control-feedback">:message</small>') !!}
            </div>
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
</div>
<input type="hidden" name="commission_rate_id" value="1">
<input type="hidden" name="active" value="0">
<input type="hidden" name="is_external" value="1">
<div class="border mt-3 mb-3"></div>

@component('component.phone-stacks')@endcomponent
@component('component.username-stacks')@endcomponent
