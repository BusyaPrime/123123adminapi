<div class="form-group row ">
    <label class="col-md-3 text-md-right col-form-label-sm" >@lang('validation.attributes.id')</label>
    <label class="col-md-4 col-form-label-sm" ><strong>{{ $user->id }}</strong></label>
</div>
<div class="form-group row ">
    <label class="col-md-3 text-md-right col-form-label-sm" >@lang('validation.attributes.name')</label>
    <label class="col-md-4 col-form-label-sm" ><strong>{{ $user->name }}</strong></label>
</div>
<div class="form-group row ">
    <label class="col-md-3 text-md-right col-form-label-sm" >@lang('validation.attributes.username')</label>
    <label class="col-md-4 col-form-label-sm" ><strong>{{ $user->username }}</strong></label>
</div>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <p>
            <strong>@lang('admin.password_change')</strong>
        </p>
    </div>
</div>

<div class="form-group row {!! $errors->first('current_password', 'has-danger')!!}" >
    <label class="col-md-3 text-md-right col-form-label-sm" for="current_password">@lang('validation.attributes.current_password')</label>
    <div class="col-md-4">
        <input type="password" name="current_password" class="form-control input-sm password" id="current_password" >
        {!! $errors->first('current_password', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('new_password', 'has-danger')!!}" >
    <label class="col-md-3 text-md-right col-form-label-sm" for="password">@lang('validation.attributes.new_password')</label>
    <div class="col-md-4">
        <input type="password" name="new_password" class="form-control input-sm password" id="password" >
        {!! $errors->first('new_password', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('new_password_confirmation', 'has-danger')!!}" >
    <label class="col-md-3 text-md-right col-form-label-sm" for="password_confirmation">@lang('validation.attributes.new_password_confirmation')</label>
    <div class="col-md-4">
        <input type="password" name="new_password_confirmation" class="form-control input-sm password" id="password_confirmation" >
        {!! $errors->first('new_password_confirmation', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
@component('component.password-stacks')@endcomponent
