@component('component.translations', ['form' => 'admin.car-types._translations_form', 'model' => $carType?? null])@endcomponent
<div class="form-group row {!! $errors->first('priority', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="priority">@lang('validation.attributes.priority')</label>
    <div class="col-md-6">
        <input type="number" step="1" min="0" name="priority" class="form-control input-sm" id="priority" value="{{ old('priority', $carType->priority ?? 0) }}"  >
        {!! $errors->first('priority', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<div class="form-group row {!! $errors->first('pickup_limit', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="pickup_limit">Беспалтное время ожидания погрузки </label>
    <div class="col-md-6">
        <div class="input-group">
            <input type="number" step="1" min="0" name="pickup_limit" class="form-control input-sm" id="pickup_limit" value="{{ old('pickup_limit', $carType->pickup_limit ?? 0) }}"  >
            <span class="input-group-text">
                минут
            </span>
        </div>
        {!! $errors->first('pickup_limit', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>


<div class="form-group row {!! $errors->first('pickup_per_minute', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="pickup_per_minute">Цена за минуту ожидания погрузки </label>
    <div class="col-md-6">
        <div class="input-group">
            <input type="number" step="1" min="0" name="pickup_per_minute" class="form-control input-sm" id="pickup_per_minute" value="{{ old('pickup_per_minute', $carType->pickup_per_minute ?? 0) }}"  >
            <span class="input-group-text">
                сум
            </span>
        </div>
        {!! $errors->first('pickup_per_minute', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>




<div class="form-group row {!! $errors->first('unloading_limit', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="unloading_limit">Беспалтное время ожидания разгрузку </label>
    <div class="col-md-6">
        <div class="input-group">
            <input type="number" step="1" min="0" name="unloading_limit" class="form-control input-sm" id="unloading_limit" value="{{ old('unloading_limit', $carType->unloading_limit ?? 0) }}"  >
            <span class="input-group-text">
                минут
            </span>
        </div>
        {!! $errors->first('unloading_limit', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('load_time_limit', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="load_time_limit">Минимальное время погрузки (Time limit)</label>
    <div class="col-md-6">
        <div class="input-group">
            <input type="number" step="1" min="0" name="load_time_limit" class="form-control input-sm" id="load_time_limit" value="{{ old('load_time_limit', $carType->load_time_limit ?? 0) }}"  >
            <span class="input-group-text">
                минут
            </span>
        </div>
        {!! $errors->first('load_time_limit', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('unloading_per_minute', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="unloading_per_minute">Цена за минуту ожидания разгрузки </label>
    <div class="col-md-6">
        <div class="input-group">
            <input type="number" step="1" min="0" name="unloading_per_minute" class="form-control input-sm" id="unloading_per_minute" value="{{ old('unloading_per_minute', $carType->unloading_per_minute ?? 0) }}"  >
            <span class="input-group-text">
                сум
            </span>
        </div>
        {!! $errors->first('unloading_per_minute', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('commission', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="commission">@lang('validation.attributes.commission') <small class="text-muted">(в %)</small></label>
    <div class="col-md-6">
        <input type="number" step="0.01" min="0" name="commission" class="form-control input-sm" id="commission" value="{{ old('commission', $carType->commission ?? 0) }}"  >
        {!! $errors->first('commission', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row">
    <label class="col-md-3 text-md-right col-form-label-sm">Частичная погрузка <small class="text-muted">(в %)</small></label>
    <div class="col-md-6 d-flex">
        <div class="row partial-percentage-box">
           @php($partialPercentages = json_decode($carType->partial_percentages ?? '[]') ?: [])
           @if (!empty($partialPercentages))
               @foreach ($partialPercentages as $partial_percentage)
                    <div class="col">
                        <input type="number" step="1" min="0" max="100" maxlength="3" name="partial_percentages[]" class="form-control" value="{{ (int)$partial_percentage }}" />
                    </div>
                @endforeach
            @else
                <div class="col">
                    <input type="number" step="1" min="0" max="100" maxlength="3" name="partial_percentages[]" class="form-control" value="0" />
                </div>
           @endif
        </div>
        <div class="btn btn-success ml-3 add-partial-percentage">+</div>
    </div>
</div>

<div class="form-group row">
        <label class="col-md-3 text-md-right col-form-label-sm" for="active">Междугородний транспорт</label>
        <div class="col-md-4">
            <div class="btn-group btn-group-sm" data-toggle="buttons">
                <label class="btn btn-outline-success {{ old('is_multi_region', $carType->is_multi_region ?? 1) == 1? 'active': '' }}">
                    <input type="radio" name="is_multi_region" value="1" {{ old('is_multi_region', $carType->is_multi_region ?? 1) == 1? 'checked': '' }} required>
                    @lang('admin.yes')
                </label>
                <label class="btn btn-outline-danger {{ old('is_multi_region', $carType->is_multi_region ?? 1) == 0? 'active': '' }}">
                    <input type="radio" name="is_multi_region" value="0" {{ old('is_multi_region', $carType->is_multi_region ?? 1) == 0? 'checked': '' }} required>
                    @lang('admin.no')
                </label>
            </div>
        </div>
</div>

<div class="form-group row">
    <div class="offset-md-3 col-md-6">
        <h5>@lang('validation.attributes.dimensions')</h5>
    </div>
</div>

<div class="form-group row {!! $errors->first('max_weight', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="max_weight">@lang('validation.attributes.max_weight') <small class="text-muted">(кг)</small></label>
    <div class="col-md-6">
        <input type="number" step="1" min="0" name="max_weight" class="form-control input-sm" id="max_weight" value="{{ old('max_weight', $carType->max_weight ?? 0) }}"  >
        {!! $errors->first('max_weight', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('dimension_x', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="dimension_x">ДхШхВ <small class="text-muted">(м)</small></label>
    <div class="col-md-6">
        {{--<input type="number" step="0.01" min="0" name="dimension_x" class="form-control input-sm" id="dimension_x" value="{{ old('dimension_x', $carType->dimension_x ?? 0) }}"  >--}}
        <select name="dimensions" id="dimensions" class="form-control input-sm" style="border-radius:7px;">
            <option value={{null}}>Нет</option>
            @foreach($sizes as $item)
                @if(isset($carType))
                    <option value="{{ json_encode(['dimension_x' => $item->dimension_x, 'dimension_y' => $item->dimension_y, 'dimension_z' => $item->dimension_z]) }}" {{ old('dimensions', json_encode(['dimension_x' => $carType->dimension_x, 'dimension_y' => $carType->dimension_y, 'dimension_z' => $carType->dimension_z])) == json_encode(['dimension_x' => $item->dimension_x, 'dimension_y' => $item->dimension_y, 'dimension_z' => $item->dimension_z]) ? 'selected': '' }}>{{ $item->title.' - '.$item->dimension_x.'x'.$item->dimension_y.'x'.$item->dimension_z }}</option>
                @else <option value="{{ json_encode(['dimension_x' => $item->dimension_x, 'dimension_y' => $item->dimension_y, 'dimension_z' => $item->dimension_z]) }}" >{{ $item->title.' - '.$item->dimension_x.'x'.$item->dimension_y.'x'.$item->dimension_z }}</option>
                @endif
            @endforeach
        </select>
        {!! $errors->first('dimensions', '<small class="form-control-feedback" style="color:#fe0032;">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('icon', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="icon">@lang('validation.attributes.thumbnail')</label>
    <div class="col-md-6">
        <input type="file" name="icon" class="form-control input-sm" id="icon"  >
        {!! $errors->first('icon', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

<div class="form-group row {!! $errors->first('icon', 'has-danger')!!}">
    <label class="col-md-3 text-md-right col-form-label-sm" for="big_icon">@lang('validation.attributes.icon')</label>
    <div class="col-md-6">
        <input type="file" name="big_icon" class="form-control input-sm" id="big_icon"/>
        {!! $errors->first('big_icon', '<small class="form-control-feedback">:message</small>') !!}
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(){
            const $button = $('.add-partial-percentage');

            $button.on('click', function(ev){
                ev.preventDefault();
                const $container = $('.partial-percentage-box');
                const $innerObject = $(`<div class="col">
                                    <input type="number" step="1" min="0" max="100" name="partial_percentages[]" class="form-control" value="0" />
                                </div>`);
                $container.append($innerObject);
                return false;
            });

        });
    </script>    
@endpush
