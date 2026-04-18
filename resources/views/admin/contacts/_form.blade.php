@foreach($contacts as $contact)
    <div class="form-group row {!! $errors->first($contact->key, 'has-danger')!!}">
        <label class="col-md-3 text-md-right col-form-label-sm" for="{{$contact->key}}">@lang('validation.attributes.'.$contact->key)</label>
        <div class="col-md-6">
            <textarea name="{{$contact->key}}" class="form-control input-sm" id="{{$contact->key}}">{{ old($contact->key, $contact->value ?? '') }}</textarea>
            {!! $errors->first($contact->key, '<small class="form-control-feedback">:message</small>') !!}
        </div>
    </div>
@endforeach
