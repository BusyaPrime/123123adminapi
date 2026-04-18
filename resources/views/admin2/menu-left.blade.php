@extends('ui.menu-left')

@section('logo'){{ asset('casva/img/logo.png') }}@endsection

@section('menu-left-items')
@if(isset($user_data) && $user_data->is_external ==1)
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route('admin2.home') }}" class="nav-link {{ $current_route_name == ''? 'active': '' }}">
                <i class="nav-icon fas fa-th"></i>
                <p>Главная</p>
            </a>
        </li>
            <li class="nav-item">
                <a href="{{ route('admin2.users.index') }}" class="nav-link  {{ $current_route_name == 'users'? 'active': '' }}">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>Пользователи</p>
                </a>
            </li>

        <li class="nav-item ">
            <span class="d-block border mb-2"></span>
        </li>
            <li class="nav-item">
                <a href="{{ route('admin2.bookings.index') }}" class="nav-link  {{ $current_route_name == 'bookings'? 'active': '' }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>Мои Заказы</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin2.transactions.balance') }}" class="nav-link  {{ $current_route_name == 'balance'? 'active': '' }}">
                    <i class="nav-icon fas fa-wallet"></i> 
                    <p>Мой баланс</p>
                </a>
            </li>

        <li class="nav-item">
            <a href="{{ route('admin2.company') }}" class="nav-link {{ $current_route_name == 'company'? 'active': '' }}">
                <i class="nav-icon fas fa-info-circle"></i>
                <p>О компании</p>
            </a>
        </li>
        <li class="nav-item">
        <a href="{{ route('admin2.auth.logout') }}" class="nav-link text-danger">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>@lang('admin.logout')</p>
        </a>
        </li>
    </ul>
@else
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route('admin2.home') }}" class="nav-link {{ $current_route_name == ''? 'active': '' }}">
                <i class="nav-icon fas fa-th"></i>
                <p>Главная</p>
            </a>
        </li>
            <li class="nav-item">
                <a href="{{ route('admin2.cars.index') }}" class="nav-link  {{ $current_route_name == 'cars'? 'active': '' }}">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>Водители</p>
                </a>
            </li>

        <li class="nav-item ">
            <span class="d-block border mb-2"></span>
        </li>
            <li class="nav-item">
                <a href="{{ route('admin2.bookings-available.index') }}" class="nav-link  {{ $current_route_name == 'bookings-available'? 'active': '' }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>Доступные Заказы</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin2.bookings.index') }}" class="nav-link  {{ $current_route_name == 'bookings'? 'active': '' }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>Мои Заказы</p>
                </a>
            </li>

        <li class="nav-item ">
            <span class="d-block border mb-2"></span>
        </li>
        
        <li class="nav-item">
                <a href="{{ route('admin2.tracking.index') }}" class="nav-link  {{ $current_route_name == 'tracking'? 'active': '' }}">
                    <i class="nav-icon fas fa-truck"></i>
                    <p>Трекинг </p>
                </a>
            </li>
        
        <li class="nav-item">
                <a href="{{ route('admin2.transactions.index') }}" class="nav-link  {{ $current_route_name == 'transactions'? 'active': '' }}">
                    <i class="nav-icon fas fa-file-invoice-dollar"></i>
                    <p>Транзакции </p>
                </a>
            </li>


        <li class="nav-item">
            <a href="{{ route('admin2.statistics.index') }}" class="nav-link  {{ $current_route_name == 'statistics'? 'active': '' }}">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Статистика/Аналитика </p>
            </a>
        </li>
{{--            <li class="nav-item">--}}
{{--                <a href="{{ route('admin.tracking.index') }}" class="nav-link  {{ $current_route_name == 'tracking'? 'active': '' }}">--}}
{{--                    <i class="nav-icon fas fa-truck"></i>--}}
{{--                    <p>Трекинг</p>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li class="nav-item">--}}
{{--                <a href="{{ route('admin.statistics.index') }}" class="nav-link  {{ $current_route_name == 'statistics'? 'active': '' }}">--}}
{{--                    <i class="nav-icon fas fa-chart-line"></i>--}}
{{--                    <p>Статистика/Аналитика </p>--}}
{{--                </a>--}}
{{--            </li>--}}

            <li class="nav-item">
                <a href="{{ route('admin2.reviews.index') }}" class="nav-link  {{ $current_route_name == 'reviews'? 'active': '' }}">
                    <i class="nav-icon fas fa-headset"></i>
                    <p>Отзывы </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin2.chat.index-bookings') }}" class="nav-link  {{ $current_route_name == 'chat'? 'active': '' }}">
                    <i class="nav-icon fas fa-comments"></i>
                    <p>Чат</p>
                </a>
            </li>

        <li class="nav-item ">
            <span class="d-block border mb-2"></span>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin2.company') }}" class="nav-link {{ $current_route_name == 'company'? 'active': '' }}">
                <i class="nav-icon fas fa-info-circle"></i>
                <p>О компании</p>
            </a>
        </li>
    </ul>
@endif
@endsection
