<div class="row">
    <div class="col-sm-3">
        <div class="form-group  {!! $errors->first('title', 'has-danger')!!}">
            <label class="  text-secondary" for="title">Название роли</label>
            <div class="">
                <input type="text" name="title" class="form-control input-sm" id="title" value="{{ old('title', $role->title ?? '') }}" required autofocus>
                {!! $errors->first('title', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="text-primary">Доступ к страницам</label>
</div>

<div class="row border rounded-lg p-4  ">
    <div class="col-sm-12 align-items-center justify-content-between ">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        @php
                            $adminRoles = [];
                            if(isset($role)) {
                                $adminRoles = json_decode($role->permissions, true);
                            }
                        @endphp
                        @foreach(\App\Domain\AdminRoles\Models\AdminRole::permissionsList() as $permission)
                        <div class="col-sm-3 mt-3">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="permissions_{{$permission}}" value="{{$permission}}" name="permissions[]" {{ in_array($permission, $adminRoles) ? 'checked':'' }}>
                                <label class="text-secondary" for="permissions_{{$permission}}">
                                    {{ trans('admin.admin_permissions.'.$permission) }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
    </div>
</div>
@component('component.password-stacks')@endcomponent
