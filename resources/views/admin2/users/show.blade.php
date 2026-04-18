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


                </form>

            </div>
        </div>
    @endcomponent
@endsection
