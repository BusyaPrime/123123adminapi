<div class="row">
    <div class="col-sm-3">

        <div class="form-group  {!! $errors->first('name', 'has-danger')!!}">
            <label class="  text-secondary" for="name">@lang('validation.attributes.name')</label>
            <div class="">
                <input type="text" name="name" class="form-control input-sm" id="name" value="{{ old('name', $user->name ?? '') }}" required autofocus>
                {!! $errors->first('name', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">

        <div class="form-group  {!! $errors->first('admin_role_id', 'has-danger')!!}">
            <label class="  text-secondary" for="admin_role_id">Роль пользователя</label>
            <div class="">
                <select class="form-control" name="admin_role_id">
                    @foreach(\App\Domain\AdminRoles\Models\AdminRole::all() as $role)
                        <option value="{{ $role->id }}" {{ ($user->admin_role_id ?? 0) == $role->id? 'selected':'' }}>{{ $role->title }}</option>
                    @endforeach
                </select>
                {!! $errors->first('admin_role_id', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">

        <div class="form-group  {!! $errors->first('username', 'has-danger')!!}">
            <label class="text-secondary" for="username">@lang('validation.attributes.username')</label>
            <div class="">
                <input type="text" name="username" class="form-control input-sm" id="username" value="{{ old('username', $user->username ?? '') }}" required>
                {!! $errors->first('username', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('password', 'has-danger')!!}" >
            <label class="text-secondary" for="password">@lang('validation.attributes.password')</label>
            <div class="">
                <input type="password" name="password" class="form-control input-sm password" id="password" {{ isset($user->id) ? '': 'required' }}>
                {!! $errors->first('password', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-sm-3">

    </div>
</div>
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
@component('component.password-stacks')@endcomponent
