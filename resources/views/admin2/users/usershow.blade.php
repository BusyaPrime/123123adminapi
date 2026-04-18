@extends('admin2.layout')

@section('center_content')
    @component('component.card', ['title' => 'Подробнее о пользователе', 'bodyClass' => 'card-body-no-padding'])

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
                    </div>

                    <p class="text-white">
                        <i class="fas fa-star text-warning mr-2"></i>
                        <span> {{ isset($user->profile->rating) && ($user->profile->rating > 0)? $user->profile->rating: '--'}}</span>
                    </p>
                </div>
				<div class="card p-3">
                    <p><span class="float-left ">ID</span><span class="float-right ">{{ $user->id }}</span></p>
                    <p><span class="float-left">Статус</span><span class="float-right">
                            {!! $user->active? '<span class="text-success">'.trans('admin.active').'</span>': '<span class="text-danger">'.trans('admin.not_active').'</span>' !!}
                        </span></p>
                    <p><span class="float-left">Дата регистрации</span><span class="float-right">{{ $user->created_at->format('d.m.Y') }}</span></p>
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
                            {{ \App\Domain\TruckBookings\Models\TruckBooking::where('user_id', $user->id)->count() ?? 0 }}
                        </span>
                        <a href="{{ route('admin2.bookings.index', ['user_id' => $user->id]) }}" class="text-secondary ml-2 float-right">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">

                        <div class="flex-grow-1">
                            Успешные
                        </div>
                        <span class="text-green  mr-2" style="font-size: 25px;">
                            {{ \App\Domain\TruckBookings\Models\TruckBooking::where('user_id', $user->id)->where('status','done')->count() ?? 0 }}
                        </span>
                        <a href="{{ route('admin2.bookings.index', ['user_id' => $user->id, 'status' => 'done']) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    {{--<div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">

                        <div class="flex-grow-1">
                            Отмененные
                        </div>
                        <span class="text-red  mr-2" style="font-size: 25px;">
                            {{ \App\Domain\TruckBookings\Models\TruckBooking::where('user_id', $user->id)->where('status','canceled')->count() ?? 0 }}
                        </span>
                        <a href="{{ route('admin2.bookings.index', ['user_id' => $user->id, 'status' => 'canceled']) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>--}}
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
                        Контакты Пользователя
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

                    {{-- <div class="add-company__2 d-flex align-items-center justify-content-between ">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-warning my-2">Назад</a>
                    </div>--}}
					<div class="add-company__2 d-flex align-items-center justify-content-between ">
                        <a href="{{ route('admin2.users.index') }}" class="btn btn-warning my-2">Назад</a>
                        <a href="{{ route('admin2.users.edit', $user) }}" class="btn btn-primary my-2 ">Редактировать данные</a>
                    </div>

                </form>

            </div>
        </div>
    @endcomponent
@endsection
