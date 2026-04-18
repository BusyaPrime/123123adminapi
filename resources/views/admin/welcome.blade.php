@extends('admin.layout')

@section('center_content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1>Добро пожаловать в CASVA</h1>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                @if(in_array('cars', $user_permissions))
                <div class="col-md-6 driver">
                    <div class="driver__box">
                        <div class="driver__title">Пригласите водителей</div>
                        <div class="driver__subtitle">Для получения новых поездок в вашем списке должен быть хотя бы один активный водитель</div>
                        <a href="{{ route('admin.cars.create') }}" class="driver__btn">Добавить водителя</a>
                    </div>
                </div>
                @endif
                @if(in_array('companies', $user_permissions))
                <div class="col-md-6 driver">
                    <div class="driver__box">
                        <div class="driver__title">Добавьте логистическую компанию</div>
                        <div class="driver__subtitle">Для получения новых заказов в вашем списке должен быть хотя бы один активный мерчант</div>
                        <a href="{{ route('admin.companies.create') }}" class="driver__btn">Добавить мерчанта</a>
                    </div>
                </div>
                    @endif
            </div>
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row stat">

                @if(in_array('users', $user_permissions))
                <div class="col-lg-3 col-6">
                    <div class="stat__box">
                        <img src="{{ asset('casva/dist/img/widget-1.png') }}" alt="1">
                        <div class="stat__text">
                            <h3 class="stat__name">Количество пользователей</h3>
                            <p class="stat__number">{{ \App\Domain\Users\Models\User::whereNotIn('role', ['admin', 'merchant'])->count() ?? 0 }}</p>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="stat__more">Подробнее</a>
                    </div>
                </div>
                @endif
                @if(in_array('cars', $user_permissions))
                <div class="col-lg-3 col-6">
                    <div class="stat__box">
                        <img src="{{ asset('casva/dist/img/widget-2.png') }}" alt="1">
                        <div class="stat__text">
                            <h3 class="stat__name">Количество водителей</h3>
                            <p class="stat__number">{{ \App\Domain\Users\Models\User::whereHas('car')->count() ?? 0 }}</p>
                        </div>
                        <a href="{{ route('admin.cars.index') }}" class="stat__more">Подробнее</a>
                    </div>
                </div>
                    @endif
                    @if(in_array('bookings', $user_permissions))
                <div class="col-lg-3 col-6">
                    <div class="stat__box">
                        <img src="{{ asset('casva/dist/img/widget-3.png') }}" alt="1">
                        <div class="stat__text">
                            <h3 class="stat__name">Количество заказов</h3>
                            <p class="stat__number">{{ \App\Domain\TruckBookings\Models\TruckBooking::count() ?? 0 }}</p>
                        </div>
                        <a href="{{ route('admin.bookings.index') }}" class="stat__more">Подробнее</a>
                    </div>
                </div>
                    @endif
{{--                <div class="col-lg-3 col-6">--}}
{{--                    <div class="stat__box">--}}
{{--                        <img src="{{ asset('casva/dist/img/widget-4.png') }}" alt="1">--}}
{{--                        <div class="stat__text">--}}
{{--                            <h3 class="stat__name">Количество пользователей</h3>--}}
{{--                            <p class="stat__number">0</p>--}}
{{--                        </div>--}}
{{--                        <a href="#" class="stat__more">Подробнее</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
                    @if(in_array('statistics', $user_permissions))
                <div class="col-lg-3 col-6">
                    <div class="stat__box">
                        <img src="{{ asset('casva/dist/img/widget-5.png') }}" alt="1">
                        <div class="stat__text">
                            <h3 class="stat__name">Сумма отчислений агрегатору</h3>
                            <p class="stat__number">{{ number_format(\App\Domain\TruckBookings\Models\TruckBooking::where('status', 'done')->sum('commission'), 0, '', ' ') }}</p>
                        </div>
                        <a href="{{ route('admin.statistics.index') }}" class="stat__more">Подробнее</a>
                    </div>
                </div>
                    @endif
{{--                <div class="col-lg-3 col-6">--}}
{{--                    <div class="stat__box">--}}
{{--                        <img src="{{ asset('casva/dist/img/widget-6.png') }}" alt="1">--}}
{{--                        <div class="stat__text">--}}
{{--                            <h3 class="stat__name">Количество пользователей</h3>--}}
{{--                            <p class="stat__number">0</p>--}}
{{--                        </div>--}}
{{--                        <a href="#" class="stat__more">Подробнее</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-3 col-6">--}}
{{--                    <div class="stat__box">--}}
{{--                        <img src="{{ asset('casva/dist/img/widget-7.png') }}" alt="1">--}}
{{--                        <div class="stat__text">--}}
{{--                            <h3 class="stat__name">Количество пользователей</h3>--}}
{{--                            <p class="stat__number">0</p>--}}
{{--                        </div>--}}
{{--                        <a href="#" class="stat__more">Подробнее</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-lg-3 col-6">--}}
{{--                    <div class="stat__box">--}}
{{--                        <img src="{{ asset('casva/dist/img/widget-8.png') }}" alt="1">--}}
{{--                        <div class="stat__text">--}}
{{--                            <h3 class="stat__name">Количество пользователей</h3>--}}
{{--                            <p class="stat__number">0</p>--}}
{{--                        </div>--}}
{{--                        <a href="#" class="stat__more">Подробнее</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
            <!-- /.row -->
            <!-- Main row -->

            @if(in_array('bookings', $user_permissions))
            <div class="row ways">
                <div class="col-md-12 ">
                    <div class="ways__name mb-4" style="color: #2b2b2b!important;font-weight: normal!important;">Заказы</div>

                </div>
                <div class="col-md-3">
                    <div class="ways__box">
                        <div class="ways__name">Ожидающие</div>
                        <div class="ways__num">{{ \App\Domain\TruckBookings\Models\TruckBooking::whereIn('status', ['order', 'new'])->count() ?? 0 }}</div>
                        <a href="{{ route('admin.bookings.index', ['status' => 'free']) }}" class="ways__btn">Подробнее</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="ways__box">
                        <div class="ways__name">Выполняются</div>
                        <div class="ways__num">{{ \App\Domain\TruckBookings\Models\TruckBooking::whereIn('status', ['waiting', 'accepted', 'arrived', 'processing', 'pause'])->count() ?? 0 }}</div>
                        <a href="{{ route('admin.bookings.index', ['status' => 'in_progress']) }}" class="ways__btn">Подробнее</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="ways__box">
                        <div class="ways__name">Выполненные</div>
                        <div class="ways__num">{{ \App\Domain\TruckBookings\Models\TruckBooking::where('status', 'done')->count() ?? 0 }}</div>
                        <a href="{{ route('admin.bookings.index', ['status' => 'done']) }}" class="ways__btn">Подробнее</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="ways__box">
                        <div class="ways__name">Отмены</div>
                        <div class="ways__num">{{ \App\Domain\TruckBookings\Models\TruckBooking::where('status', 'canceled')->count() ?? 0 }}</div>
                        <a href="{{ route('admin.bookings.index', ['status' => 'canceled']) }}" class="ways__btn">Подробнее</a>
                    </div>
                </div>
            </div>
            @endif
            <!-- /.row (main row) -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

{{--    <div class=" col-md-12 col-lg-10 offset-lg-1">--}}
{{--        <h4>Сегодня</h4>--}}
{{--        <div class="row">--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.bookings.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-success">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\TruckBookings\Models\TruckBooking::whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->count() }} <i class="icmn-clipboard  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Бронирований</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.users.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-primary">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\Users\Models\User::whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->count() }} <i class="icmn-users  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Пользователей</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.cars.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-info">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\Cars\Models\Car::whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->count() }} <i class="icmn-truck  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Автомобилей</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.companies.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-warning">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\Companies\Models\Company::whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->count() }} <i class="icmn-office  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Компаний</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <div class=" col-md-12 col-lg-10 offset-lg-1">--}}
{{--        <h4>За последние 7 дней</h4>--}}
{{--        <div class="row">--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.bookings.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-success">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\TruckBookings\Models\TruckBooking::whereBetween('created_at', [date('Y-m-d 00:00:00', now()->subDays(7)->timestamp), date('Y-m-d 23:59:59')])->count() }} <i class="icmn-clipboard  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Бронирований</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.users.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-primary">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\Users\Models\User::whereBetween('created_at', [date('Y-m-d 00:00:00', now()->subDays(7)->timestamp), date('Y-m-d 23:59:59')])->count() }} <i class="icmn-users  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Пользователей</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.cars.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-info">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\Cars\Models\Car::whereBetween('created_at', [date('Y-m-d 00:00:00', now()->subDays(7)->timestamp), date('Y-m-d 23:59:59')])->count() }} <i class="icmn-truck  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Автомобилей</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.companies.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-warning">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\Companies\Models\Company::whereBetween('created_at', [date('Y-m-d 00:00:00', now()->subDays(7)->timestamp), date('Y-m-d 23:59:59')])->count() }} <i class="icmn-office  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Компаний</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <div class=" col-md-12 col-lg-10 offset-lg-1">--}}
{{--        <h4>Всего</h4>--}}
{{--        <div class="row">--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.bookings.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-success">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\TruckBookings\Models\TruckBooking::count() }} <i class="icmn-clipboard  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Бронирований</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.users.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-primary">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\Users\Models\User::count() }} <i class="icmn-users  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Пользователей</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.cars.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-info">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\Cars\Models\Car::count() }} <i class="icmn-truck  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Автомобилей</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div class="col-6 col-lg-3">--}}
{{--                <a href="{{ route('admin.companies.index') }}">--}}
{{--                    <div class="p-5 mb-3 text-center card-badge text-warning">--}}
{{--                        <div class="mr-2 mb-2 font-size-26  d-block">--}}
{{--                            {{ \App\Domain\Companies\Models\Company::count() }} <i class="icmn-office  "></i>--}}
{{--                        </div>--}}
{{--                        <span class=" d-block">Компаний</span>--}}
{{--                    </div>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection
