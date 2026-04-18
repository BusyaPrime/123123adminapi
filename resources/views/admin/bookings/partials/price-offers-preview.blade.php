@if($priceOffers->isNotEmpty())
    @foreach($priceOffers as $priceOffer)
        @if($priceOffer->driver)
            <div class="car">
                <div class="car-container">
                    <div class="avatar" style="border-radius: 0!important">
                        <img src="{{ $priceOffer->driver->car->carType->imageUrl() }}" />
                    </div>
                    <div class="driver-name">{{ $priceOffer->driver->profile->surname ?? '' }} {{ $priceOffer->driver->profile->name ?? '' }}</div>
                    |<div class="driver-name">{{ number_format($priceOffer->amount ?? 0, 0, '', ' ') }}</div>
                    |<div class="driver-name">{{ number_format($priceOffer->client_amount ?? 0, 0, '', ' ') }}</div>
                    |<div class="time">{{ $priceOffer->created_at->format('d.m H:i') }}</div>
                </div>
            </div>
        @else
            <div class="car">
                <div class="car-container">
                    <div class="avatar">
                        <img src="https://admin.casva.uz/uploads/defaults/user.png" />
                    </div>
                    <div class="driver-name">Удалённый пользователь</div>
                    <div class="time">{{ $priceOffer->created_at->format('d.m H:i') }}</div>
                </div>
            </div>
        @endif
    @endforeach
@else
    <div class="car">
        <div class="car-container">
            <div>Нет предложений</div>
        </div>
    </div>
@endif
