@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Подробнее о водителе', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')

            <div class="col-sm-6 d-flex align-items-end justify-content-end">
                <div class="balanse mb-0 mr-2 border rounded-pill px-3 py-2">
                    <span class="text-primary" style="font-weight: bold;">Баланс </span>
                    @if($user->profile->company_id ?? false)
                        <span class="text-primary ml-4" >Использует баланс компании</span>
                    @else
                    <span class="text-primary ml-4" style="font-size: 25px;">{{ number_format($user->profile->balance, 0, '', ' ') ?? 0 }} сум</span>
                    @endif
                </div>
            </div>
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
                        <small class="text-white d-block  ">
                            @if($user->profile->company_id ?? false)
                                {{ $user->profile->company->title ?? '--' }}
                                <a href="{{ route('admin.companies.show', $user->profile->company_id) }}" class="text-white ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @else
                                Самозанятый
                            @endif
                        </small>
                    </div>

                    <div class="text-center mb-3">
                        <span class="text-white d-block" style="font-size: 14px;">Количество заработанных средств</span>
                        <div class="text-green">{{ number_format(\App\Domain\TruckBookings\Models\TruckBooking::where('driver_id', $car->user_id)->where('status', 'done')->sum('price'), 0, '', ' ') ?? 0}} сум
                            <a href="{{ route('admin.bookings.index', ['driver_id' => $car->user_id]) }}" class="text-white ml-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        <span class="text-white d-block" style="font-size: 14px;">Сумма отчислений</span>
                        <div class="text-green">{{ number_format(\App\Domain\TruckBookings\Models\TruckBooking::where('driver_id', $car->user_id)->where('status', 'done')->sum('commission'), 0, '', ' ') ?? 0}} сум
                            <a href="{{ route('admin.transactions.index', ['user_id' => $car->user_id]) }}" class="text-white ml-2">
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
                    <p><span class="float-left">Последний вход</span><span class="float-right">{{ $user->last_seen_at ? $user->last_seen_at->format('d.m.Y'): '--' }}</span></p>
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
                            {{ \App\Domain\TruckBookings\Models\TruckBooking::where('driver_id', $car->user_id)->count() ?? 0 }}
                        </span>
                        <a href="{{ route('admin.bookings.index', ['driver_id' => $car->user_id]) }}" class="text-secondary ml-2 float-right">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">

                        <div class="flex-grow-1">
                            Завершенные
                        </div>
                        <span class="text-green  mr-2" style="font-size: 25px;">
                            {{ \App\Domain\TruckBookings\Models\TruckBooking::where('driver_id', $car->user_id)->where('status','done')->count() ?? 0 }}
                        </span>
                        <a href="{{ route('admin.bookings.index', ['driver_id' => $car->user_id, 'status' => 'done']) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">

                        <div class="flex-grow-1">
                            Отмененные
                        </div>
                        <span class="text-red  mr-2" style="font-size: 25px;">
                            {{ \App\Domain\TruckBookings\Models\TruckBooking::where('driver_id', $car->user_id)->where('status','canceled')->count() ?? 0 }}
                        </span>
                        <a href="{{ route('admin.bookings.index', ['driver_id' => $car->user_id, 'status' => 'canceled']) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="border rounded-lg px-3 py-2">
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

                        <div class="col-sm-6">
                            <div class="form-group">
                                @if($user->profile->company_id ?? false)
                                    <input type="text" class="form-control" placeholder="Процентная ставка: Процентная ставка компании" disabled style="background: transparent">
                                @else
                                    <input type="text" class="form-control" placeholder="Процентная ставка: {{ isset($car->carType)?  $car->carType->commission.'%' : 'Не указан тип машины' }}" disabled style="background: transparent">
                                @endif
                            </div>
                        </div>

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
                                <input type="text" class="form-control" placeholder="Водительское удостоверение: {{ isset($user->profile) && $user->profile->licence ? 'Загружено' : 'Не загружено' }}" disabled style="background: transparent">
                                @if($user->profile->licence)
                                    <div class="input-group-append">
                                        <a href="{{ $user->profile->licenceUrl() }}" target="_blank" class="text-secondary bg-transparent input-group-text">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                    <div class="input-group-append">
                                        <button type="submit" onclick="if(confirm('Вы хотите удалить водтельское удостоверение ?')) $(this).find('form').submit()" class="text-danger bg-transparent input-group-text">
                                            <i class="fas fa-trash"></i>
                                            <div style="hidden">
                                                <form action="{{ route('admin.cars.destroyLicence', $car) }}" method="POST">
                                                    @csrf()
                                                    @method('delete')
                                                </form>
                                            </div>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Тех. паспорт: {{ isset($user->profile) && $user->profile->car_licence ? 'Загружено' : 'Не загружено' }}" disabled style="background: transparent">
                                @if($user->profile->car_licence)
                                    <div class="input-group-append">
                                        <a href="{{ $user->profile->carLicenceUrl() }}" target="_blank" class="text-secondary bg-transparent input-group-text">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                    <div class="input-group-append">
                                        <button type="submit" onclick="if(confirm('Вы хотите удалить тех. паспорт ?')) $(this).find('form').submit()" class="text-danger bg-transparent input-group-text">
                                            <i class="fas fa-trash"></i>
                                            <div style="hidden">
                                                <form action="{{ route('admin.cars.destroyCarLicence', $car) }}" method="POST">
                                                    @csrf()
                                                    @method('delete')
                                                </form>
                                            </div>
                                        </button>
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
                        <a href="{{ route('admin.cars.index') }}" class="btn btn-warning my-2">Назад</a>
                        <a href="{{ route('admin.cars.edit', $car) }}" class="btn btn-primary my-2 ">Редактировать данные</a>
                    </div>
                </div>

                <div class="border rounded-lg px-3 py-2 mt-3">

                    <div class="float-right">
                        @component('component.modal', ['id' => 'push_'.$user->id, 'class' => 'btn btn-sm btn-success'])
                            @slot('label')
                                <i class="icmn-bubble"></i> Отправить пуш
                            @endslot
                            @slot('title')
                                Отправить пуш
                            @endslot
                            <div id="push_{{$user->id}}">
                                <form  action="{{ route('admin.cars.send-push') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{$user->id}}">
                                    <div class="form-group row ">
                                        <label class="col-md-3 text-md-right col-form-label-sm" for="title">Тема</label>
                                        <div class="col-md-9">
                                            <input type="text" name="title" class="form-control input-sm" id="title" value="{{ old('title', '') }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-3 text-md-right col-form-label-sm" for="message">Сообщение</label>
                                        <div class="col-md-9">
                                            <textarea class="form-control" name="message" id="message" cols="30" rows="10" required>{{ old('message') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-12 text-center">
                                            <button class="btn btn-success" type="submit" >Отправить</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endcomponent
                    </div>
                    <div class="text-primary font-weight-bold mb-2">
                        Уведомления
                    </div>
                    <div class="clearfix"></div>
                    <hr>

                    @if($notifications->isNotEmpty())
                        <div class="table-responsive ">
                            <table class="table  mt-0">
                                <thead class="table__thead ">
                                <tr>
                                    <th class="table__th">Уведомление</th>
                                    <th class="table__th">Дата</th>
                                    <th class="table__th"></th>
                                </tr>
                                </thead>
                                <tbody class="table__tbody">
                                @foreach($notifications as $notification)
                                    <tr class="table__tr mt-2 mb-2 ">
                                        <td class="table__td text-left">
                                            <p class=""><strong>{{ $notification->title }}</strong></p>
                                            <div>{!! nl2br($notification->message) !!}</div>
                                        </td>
                                        <td class="table__td">{{ optional($notification->created_at)->format('H:i d.m.Y') }}</td>
                                        <td class="table__td">
                                            <form action="{{ route('admin.cars.destroy-push', [$user, $notification]) }}" class="d-inline-block" method="post">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-sm btn-danger"
                                                        onclick="return confirm('@lang('admin.destroy_confirm')');"
                                                        data-toggle="tooltip" data-placement="top" title="@lang('admin.delete')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>
                        @include('ui.pagination', ['data' => $notifications])
                    @else
                        <p class="text-center">Нет уведомлений</p>
                    @endif

                </div>


            </div>
        </div>
    @endcomponent
@endsection
