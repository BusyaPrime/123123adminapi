@extends('admin2.layout')

@section('center_content')
    @component('component.card', ['title' => 'Подробнее о водителе', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')

{{--            <div class="col-sm-6 d-flex align-items-end justify-content-end">--}}
{{--                <div class="balanse mb-0 mr-2 border rounded-pill px-3 py-2">--}}
{{--                    <span class="text-primary" style="font-weight: bold;">Баланс </span>--}}
{{--                    @if($user->profile->company_id ?? false)--}}
{{--                        <span class="text-primary ml-4" >Использует баланс компании</span>--}}
{{--                    @else--}}
{{--                    <span class="text-primary ml-4" style="font-size: 25px;">{{ $user->profile->balance ?? 0 }} сум</span>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            </div>--}}
        @endslot
        <div class="row">
            <div class="col-sm-3">
                <div class="card bg-primary d-flex flex-column justify-content-between align-items-center pt-2 pb-2">
                    <div class="d-flex flex-column">
                        <div class="px-5 pt-3 mb-3">
                            <img src="{{ $user->profile->imageUrl() }}" class="img-fluid img-thumbnail rounded-circle" alt="">
                        </div>
                    </div>
                    <div class="text-white text-center  mb-3">
                        <span class="text-uppercase">{{ ($user->profile->surname ?? '').' '.($user->profile->name ?? '').' '.($user->profile->middle_name ?? '') }}</span>
{{--                        <small class="text-white d-block  ">--}}
{{--                            @if($user->profile->company_id ?? false)--}}
{{--                                {{ $user->profile->company->title ?? '--' }}--}}
{{--                                <a href="#" class="text-white ml-2">--}}
{{--                                    <i class="fas fa-eye"></i>--}}
{{--                                </a>--}}
{{--                            @else--}}
{{--                                Самозанятый--}}
{{--                            @endif--}}
{{--                        </small>--}}
                    </div>

                    <div class="text-center mb-3">
                        <span class="text-white d-block" style="font-size: 14px;">Количество заработанных средств</span>
                        <div class="text-green">{{ $car->done_price_sum ?? 0 }} сум
                            <a href="{{ route('admin2.bookings.index', ['driver_id' => $car->user_id]) }}" class="text-white ml-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        <span class="text-white d-block" style="font-size: 14px;">Сумма отчислений</span>
                        <div class="text-green">{{ $car->done_commission_sum ?? 0 }} сум
                            <a href="{{ route('admin2.transactions.index', ['user_id' => $car->user_id]) }}" class="text-white ml-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>

                    <p class="text-white">
                        <i class="fas fa-star text-warning mr-2"></i>
                        <span> {{ isset($user->profile->rating) && ($user->profile->rating > 0)? $user->profile->rating: '--'}}</span>
                    </p>
                </div>
                <div class="card p-3">
                    <p><span class="float-left ">ID</span><span class="float-right ">{{ $user->id }}</span></p>
                    <p><span class="float-left">Статус</span><span class="float-right">
                            {!! $car->active? '<span class="text-success">'.trans('admin.active').'</span>': '<span class="text-danger">'.trans('admin.not_active').'</span>' !!}
                        </span></p>
                    <p><span class="float-left">Дата регистрации</span><span class="float-right">{{ $car->created_at->format('d.m.Y') }}</span></p>
                </div>
                <div class="text-primary text-center mt-4 mb-2" style="font-size: 21px;">
                    Заказы
                </div>
                <div class="card p-3">
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">
                        <div class="flex-grow-1">
                            Всего
                        </div>
                        <span class="text-orange  mr-2" style="font-size: 25px;">
                            {{ $car->orders_count ?? 0 }}
                        </span>
                        <a href="{{ route('admin2.bookings.index', ['driver_id' => $car->user_id]) }}" class="text-secondary ml-2 float-right">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">

                        <div class="flex-grow-1">
                            Завершенные
                        </div>
                        <span class="text-green  mr-2" style="font-size: 25px;">
                            {{ $car->done_orders_count ?? 0 }}
                        </span>
                        <a href="{{ route('admin2.bookings.index', ['driver_id' => $car->user_id, 'status' => 'done']) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">

                        <div class="flex-grow-1">
                            Отмененные
                        </div>
                        <span class="text-red  mr-2" style="font-size: 25px;">
                            {{ $car->canceled_orders_count ?? 0 }}
                        </span>
                        <a href="{{ route('admin2.bookings.index', ['driver_id' => $car->user_id, 'status' => 'canceled']) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-9">
                <form class="border rounded-lg px-3 py-2">
                    <div class="text-primary font-weight-bold mb-2">
                        Информация
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                    <input type="text" class="form-control" style="background: transparent" placeholder="Регион водителя: {{ ($user->profile && $user->profile->region)? ($user->profile->region->title ?? ''):'Не указан' }}" disabled>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                    <input type="text" class="form-control" style="background: transparent" placeholder="Дата рождения: {{ $user->profile->birthday ?? 'Не указан' }}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="text-primary font-weight-bold mb-2">
                        Транспорт
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                    <input type="text" class="form-control" style="background: transparent" placeholder="Тип машины: {{ $car->carType->title ?? 'Не указан' }}" disabled>
                            </div>
                        </div>

{{--                        <div class="col-sm-6">--}}
{{--                            <div class="form-group">--}}
{{--                                @if($user->profile->company_id ?? false)--}}
{{--                                    <input type="text" class="form-control" placeholder="Процентная ставка: Процентная ставка компании" disabled style="background: transparent">--}}
{{--                                @else--}}
{{--                                    <input type="text" class="form-control" placeholder="Процентная ставка: {{ isset($car->carType)?  $car->carType->commission.'%' : 'Не указан тип машины' }}" disabled style="background: transparent">--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" style="background: transparent" placeholder="Тип погрузки: {{ $car->loadTypes->isNotEmpty()  ? ($car->loadTypes->first())->title: 'Не указан' }}" disabled>
                            </div>
                        </div>

{{--                        <div class="col-sm-6">--}}
{{--                            <div class="form-group">--}}
{{--                                <input type="text" class="form-control" style="background: transparent" placeholder="Грузоподъемность: {{ $car->max_weight ?? 0 }} кг." disabled>--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" style="background: transparent" placeholder="Марка: {{ $car->brand ?? 'Не указан' }}" disabled>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" style="background: transparent" placeholder="Модель: {{ $car->model ?? 'Не указан' }}" disabled>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" style="background: transparent" placeholder="Гос номер автомобиля: {{ $car->number ?? 'Не указан' }}" disabled>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" style="background: transparent" placeholder="Гос номер прицепа: {{ $car->trailer_number ?? 'Не указан' }}" disabled>
                            </div>
                        </div>

{{--                        <div class="col-sm-6">--}}
{{--                            <div class="form-group">--}}
{{--                                <input type="text" class="form-control" style="background: transparent" placeholder="ДШВ: {{ ($car->dimension_x ?? 0).'x'.($car->dimension_y ?? 0).'x'.($car->dimension_z ?? 0) }} м." disabled>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>


                    <div class="text-primary font-weight-bold mb-2">
                        Документы Водителя
                    </div>


                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Водительское удостоверение{{ isset($user->profile) && $user->profile->licence_number ? ': '.$user->profile->licence_number:': Номер не указан' }}" disabled style="background: transparent">
                                @if($user->profile->licence ?? false)
                                    <div class="input-group-append">
                                        <a href="{{ $user->profile->licenceUrl() }}" target="_blank" class="text-secondary bg-transparent input-group-text">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Тех. паспорт{{ isset($user->profile) && $user->profile->car_licence_number ? ': '.$user->profile->car_licence_number:': Номер не указан' }}" disabled style="background: transparent">
                                @if($user->profile->car_licence ?? false)
                                    <div class="input-group-append">
                                        <a href="{{ $user->profile->carLicenceUrl() }}" target="_blank" class="text-secondary bg-transparent input-group-text">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="text-primary font-weight-bold mb-2">
                        Контакты Водителя
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Номер телефона: {{ $user->username ?? '' }}
                                    " disabled style="background: transparent">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Email: {{ $user->email ?? '' }}
                                    " disabled style="background: transparent">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Telegram: {{ $user->profile->telegram ?? '' }}
                                    " disabled style="background: transparent">
                            </div>
                        </div>

                    </div>

                    <div class="text-primary font-weight-bold mb-2">
                        @lang('validation.attributes.supported_cargo_types')
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            @forelse($car->cargoTypes as $cargoType)
                                <div class="badge badge-default">{{ $cargoType->title ?? '--' }}</div>
                            @empty
                                Не указаны
                            @endforelse
                        </div>
                    </div>


                    <div class="add-company__2 d-flex align-items-center justify-content-between ">
                        <a href="{{ route('admin2.cars.index') }}" class="btn btn-warning my-2">Назад</a>
                        <a href="{{ route('admin2.cars.edit', $car) }}" class="btn btn-primary my-2 ">Редактировать данные</a>
                    </div>
                </form>

            </div>
        </div>
    @endcomponent
@endsection
