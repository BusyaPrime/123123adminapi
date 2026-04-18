<input type="hidden" name="routes[0][address]" value="{{$data['region_from'] ?? 'Не определено'}}">
<input type="hidden" name="routes[0][lat]" value="{{$data['region_from_lat'] ?? 41.326681}}">
<input type="hidden" name="routes[0][lng]" value="{{$data['region_from_lng'] ?? 69.244031}}">
<input type="hidden" name="routes[0][order]" value="0">

<input type="hidden" name="routes[1][address]" value="{{$data['region_to'] ?? 'Не определено'}}">
<input type="hidden" name="routes[1][lat]" value="{{$data['region_to_lat'] ?? 41.326681}}">
<input type="hidden" name="routes[1][lng]" value="{{$data['region_to_lng'] ?? 69.244031}}">
<input type="hidden" name="routes[1][order]" value="1">

<input type="hidden" name="region_from_id" value="{{$data['region_from_id'] ?? ''}}">
<input type="hidden" name="region_to_id" value="{{$data['region_to_id'] ?? ''}}">

<input type="hidden" name="distance" value="{{$data['distance'] ?? 0}}">

<input type="hidden" name="dimension_x" value="{{$data['dimension_x'] ?? 0}}">
<input type="hidden" name="dimension_y" value="{{$data['dimension_y'] ?? 0}}">
<input type="hidden" name="dimension_z" value="{{$data['dimension_z'] ?? 0}}">
<input type="hidden" name="weight" value="{{$data['weight'] ?? 0}}">

<input type="hidden" name="load_type_id" value="{{$data['load_type_id'] ?? ''}}">
<input type="hidden" name="cargo_type_id" value="{{$data['cargo_type_id'] ?? ''}}">

<input type="hidden" name="date" value="{{$date ?? ''}}">
<input type="hidden" name="time" value="{{$time ?? ''}}">



<div class="row">
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('username', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="username">Телефон заказчика</label>
            <div class="">
                <input type="text"  name="username" class="form-control input-sm" id="username" value="{{ old('username', $booking->username ?? '') }}" required >
                {!! $errors->first('username', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('name', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="name">ФИО Заказчика</label>
            <div class="">
                <input type="text"  name="name" class="form-control input-sm" id="name" value="{{ old('name', $booking->name ?? '') }}" required >
                {!! $errors->first('name', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('receiver_phone', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="receiver_phone">Телефон получателя</label>
            <div class="">
                <input type="text"  name="receiver_phone" class="form-control input-sm" id="receiver_phone" value="{{ old('receiver_phone', $booking->receiver_phone ?? '') }}" required >
                {!! $errors->first('receiver_phone', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('payment_type', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="payment_type">Тип оплаты</label>
            <select name="payment_type" id="payment_type" class="form-control" required>
                @foreach(\App\Domain\TruckBookings\Models\TruckBooking::paymentTypes() as $payType)
                    <option value="{{$payType}}" {{ old('payment_type', $booking->payment_type ?? '') == $payType ? 'selected': '' }}>{{ trans("admin.payment_types.".$payType) }}</option>
                @endforeach
            </select>
            <div class="">
                {!! $errors->first('payment_type', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('payer', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="payer">Кто оплачивает</label>
            <select name="payer" id="payer" class="form-control" required>
                    <option value="sender" {{ old('payer', $booking->payer ?? '') == 'sender' ? 'selected': '' }}>Отправитель</option>
                    <option value="receiver" {{ old('payer', $booking->payer ?? '') == 'receiver' ? 'selected': '' }}>Получатель</option>
            </select>
            <div class="">
                {!! $errors->first('payment_type', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('not_full', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="not_full">Загруженность</label>
            <select name="not_full" id="not_full" class="form-control" required>

                <option value="0" {{ old('not_full', $booking->not_full ?? '0') == 0 ? 'selected': '' }}>Полная</option>
                <option value="1" {{ old('not_full', $booking->not_full ?? '0') == 1 ? 'selected': '' }}>Частичная</option>
            </select>
            <div class="">
                {!! $errors->first('not_full', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>


</div>

<div class="row">
    <div class="col-md-12 mb-3">
        <strong class="mb-3 d-block">Тарифы</strong>
        <input type="hidden" id="car_type_id" name="car_type_id" value="">
        <div class="row js_rates_wrapper">
            @forelse($rates as $rate)
                <div class="col-md-3">
                    <div class="card mb-3 border  js-rate_block" data-rate="{{$rate->id}}" data-price="{{ $rate->calculatedPrice }}" data-notfullprice="{{ $rate->calculatedPriceNotFull }}">
                        <div class="card-body">
                            <div>
                                <strong>{{ $rate->title }}</strong>
                            </div>
                            <div class="text-success js-price_tag" >
                                {{ $rate->calculatedPrice }} сум
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-3">
                    <p class="text-muted mb-3">
                        Нет подходящих тарифов
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">

        <div class="form-group  {!! $errors->first('price', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="price">Цена</label>
            <div class="">
                <input type="text"  name="price" class="form-control input-sm js-price-input" id="price"
                       value="{{ old('price', $booking->price ?? 0) }}" required >
                {!! $errors->first('price', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="comment">Комментарий к заказу</label>
            <textarea type="text" class="form-control" name="comment" id="comment" placeholder="Впишите комментарий к заказу" rows="5" ></textarea>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12 my-5">
                <div class="icheck-primary d-inline">
                    <input type="checkbox" id="need_provide_loader" value="1" name="need_provide_loader" >
                    <label class="text-secondary" for="need_provide_loader">
                        Требуются грузчики
                    </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="icheck-primary d-inline">
                    <input type="checkbox" id="need_pack" value="1" name="need_pack" >
                    <label class="text-secondary" for="need_pack">
                        Требуется упаковка
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
        <script src="{{ asset('assets/js/easy-number-separator.js') }}"></script>
        <script>
            $(function () {
                easyNumberSeparator({
                    selector: ".js-price-input",
                    separator: " ",
                })
            });
        </script>
    <script>
        $(function () {
            $('.js-rate_block').on('click', function () {
                var price = $(this).data('price') * 1;
                var priceNotFull = $(this).data('notfullprice') * 1;
                var carTypeId = $(this).data('rate') * 1;

                var notFullSelect = $('#not_full');
                var priceInput = $('#price');
                var carTypeIdInput = $('#car_type_id');
                carTypeIdInput.val(carTypeId);

                if(notFullSelect.val() == 1) {
                    priceInput.val(priceNotFull);
                    document.getElementById('price').dispatchEvent(new Event('input', {bubbles:true}));
                } else {
                    priceInput.val(price);
                    document.getElementById('price').dispatchEvent(new Event('input', {bubbles:true}));
                }

                setRate($(this));
            });

            function setRate(el)
            {
                $('.js-rate_block').removeClass('border-success');
                el.addClass('border-success');
            }

            $('.js-rate_block').first().trigger('click');


            $('#not_full').on('change', function () {
                var notFullVal = $(this).val();

                $('.js-rate_block').each(function () {
                    var price = $(this).data('price') * 1;
                    var priceNotFull = $(this).data('notfullprice') * 1;

                    if(notFullVal == 1) {
                        // priceInput.val(priceNotFull);
                        $(this).find('.js-price_tag').text(priceNotFull + ' сум')
                    } else {
                        // priceInput.val(price);
                        $(this).find('.js-price_tag').text(price + ' сум')
                    }
                    if($(this).hasClass('border-success')) {
                        $(this).trigger('click');
                    }

                });
            });
        });
    </script>
@endpush
@push('styles')
    <style>
        .js-rate_block {
            cursor: pointer;
        }
    </style>
@endpush
