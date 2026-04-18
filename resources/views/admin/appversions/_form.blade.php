<h5 >Details Per Version</h5>

<div class="row">
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('username', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="username">Version number</label>
            <div class="">
                <input type="text" name="version_no" class="form-control decimal input-sm" id="username" {{ isset($user) ? 'readonly': '' }} value="{{ old('username', $user->username ?? '') }}" required>
                {!! $errors->first('username', '<small class="form-control-feedback">:message</small>') !!}
            </div>
            <div class="small text-muted"></div>
        </div>
		<label class=" text-md-right text-secondary" for="active">Platform type</label><br>
		<div class="btn-group btn-group-sm" data-toggle="buttons">
            <label class="btn">
                <input type="radio" name="app_type" value="android" checked required>
                Android
            </label>
			<label class="btn">
                <input type="radio" name="app_type" value="ios" required>
                iOS
            </label>
        </div>
		<label class=" text-md-right text-secondary" for="active">Application type</label><br>
		<div class="btn-group btn-group-sm" data-toggle="buttons">
            <label class="btn">
                <input type="radio" name="userrole" value="customer" checked required>
                Customer
            </label>
			<label class="btn">
                <input type="radio" name="userrole" value="driver" required>
                Driver
            </label>
        </div>
    </div><br>
	
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$('.decimal').keypress(function(event) {
    if(event.which < 46
    || event.which > 59) {
        event.preventDefault();
    } 
});
</script>


@component('component.phone-stacks')@endcomponent
@component('component.add-remove-element-stacks', ['min' => 1, 'element' => 'input-group', 'wrapperId' => 'phones', 'removeClass' => 'remove_phone', 'addClass' => 'add_phone', 'inputmask' => '+\\\9\\\9\\\8 (99) 999-99-99'])@endcomponent
@component('component.add-remove-element-stacks', ['min' => 1, 'element' => 'input-group', 'wrapperId' => 'emails', 'removeClass' => 'remove_email', 'addClass' => 'add_email'])@endcomponent