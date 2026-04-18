<div class="form-group row {!! $errors->first('title', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="title">@lang('validation.attributes.title')</label>
    <div class="col-md-6">
        <input type="text" name="title" class="form-control input-sm" id="title" value="{{ old('title', $season->title ?? '') }}"  required>
        {!! $errors->first('title', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<div class="form-group row {!! $errors->first('month_start', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="month_start">Начало</label>
    <div class="col-md-6">
        <select name="month_start" id="month_start" class="form-control input-sm" required>
            @foreach(trans('admin.months') as $k => $month)
                <option value="{{$k}}" {{ old('month_start', $season->month_start ?? 1) == $k ? 'selected': '' }}>{{$month}}</option>
            @endforeach
        </select>
        {!! $errors->first('month_start', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
<div class="form-group row {!! $errors->first('month_end', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="month_end">Конец (включительно)</label>
    <div class="col-md-6">
        <select name="month_end" id="month_end" class="form-control input-sm" required>
            @foreach(trans('admin.months') as $k => $month)
                <option value="{{$k}}" {{ old('month_end', $season->month_end ?? 1) == $k ? 'selected': '' }}>{{$month}}</option>
            @endforeach
        </select>
        {!! $errors->first('month_end', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>
