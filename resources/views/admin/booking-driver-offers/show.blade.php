@extends('admin.layout')

@section('center_content')
    @php
        $driver = $driverOffer->driver;
        $driverProfile = $driver->profile ?? null;
        $driverCar = $driver->car ?? null;
        $booking = $booking ?? null;
    @endphp

    @component('component.card', ['title' => 'Предложение водителя #'.$driverOffer->id])
        @slot('buttons')
            <div class="col-sm-6 d-flex align-items-end justify-content-end">
                @if($booking)
                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-primary mr-2">Открыть заказ</a>
                @endif
                <a href="{{ route('admin.bookings.driverPriceOffers') }}" class="btn btn-warning">Назад</a>
            </div>
        @endslot

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="form-control rounded">ID предложения: {{ $driverOffer->id }}</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="form-control rounded">ID заказа: {{ $driverOffer->booking_id ?? '--' }}</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="form-control rounded">ID водителя: {{ $driverOffer->driver_id ?? '--' }}</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="form-control rounded">Предложенная сумма: {{ number_format($driverOffer->amount ?? 0, 0, '', ' ') }} сум</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="form-control rounded">Сумма для клиента: {{ number_format($driverOffer->client_amount ?? 0, 0, '', ' ') }} сум</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="form-control rounded">Создано: {{ $driverOffer->created_at ? $driverOffer->created_at->format('d.m.Y H:i') : '--' }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <h5 class="text-primary">Водитель</h5>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="form-control rounded">Ф.И.О.: {{ trim(($driverProfile->surname ?? '').' '.($driverProfile->name ?? '').' '.($driverProfile->middle_name ?? '')) ?: '--' }}</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="form-control rounded">Телефон: {{ $driver->username ?? ($driverProfile->phone_number ?? '--') }}</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="form-control rounded">Рейтинг: {{ $driver->rating ?? '--' }}</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="form-control rounded">Машина: {{ $driverCar ? trim(($driverCar->model ?? '').' '.($driverCar->number ?? '')) : '--' }}</div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('admin.bookings.driverPriceOffers') }}" class="btn btn-warning">Назад к списку</a>
            <div>
                @if($driver)
                    <a href="{{ route('admin.users.show', $driver) }}" class="btn btn-info mr-2">Открыть водителя</a>
                @endif
                @if($driverCar)
                    <a href="{{ route('admin.cars.show', $driverCar) }}" class="btn btn-secondary">Открыть машину</a>
                @endif
            </div>
        </div>
    @endcomponent
@endsection
