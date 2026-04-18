@extends('ui.menu-left')

@section('logo'){{ asset('casva/img/logo.png') }}@endsection

@push('styles')
    <style>
        .sidebar .sidebar-nav-link {
            display: flex;
            align-items: center;
            min-height: 44px;
            padding: 10px 14px;
        }

        .sidebar .sidebar-nav-link > .nav-icon {
            flex: 0 0 1.45rem;
            width: 1.45rem;
            margin: 0 12px 0 0;
            text-align: center;
        }

        .sidebar .sidebar-nav-label {
            display: flex;
            align-items: center;
            flex: 1 1 auto;
            min-width: 0;
            margin: 0;
            line-height: 1.2;
        }

        .sidebar .sidebar-nav-text {
            flex: 1 1 auto;
            min-width: 0;
        }

        .sidebar .sidebar-nav-arrow {
            margin-left: 10px;
            position: static !important;
            top: auto !important;
            right: auto !important;
        }

        .sidebar .sidebar-nav-link--sub {
            min-height: 42px;
            padding-left: 28px;
            padding-right: 14px;
        }

        .sidebar .sidebar-nav-link--sub > .nav-icon {
            flex-basis: 1rem;
            width: 1rem;
            font-size: 0.9rem;
        }

        .sidebar .sidebar-nav-treeview .nav-item {
            margin: 0;
        }

        .sidebar .sidebar-counter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 21px;
            height: 21px;
            padding: 0 6px;
            margin-left: 10px;
            border-radius: 999px;
            background: #ffc107;
            color: #fff !important;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('menu-left-items')

        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
              with font-awesome or any other icon font library -->
            <li class="nav-item">
                <a href="{{ route('admin.home') }}" class="nav-link {{ $current_route_name == '' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-th"></i>
                    <p>Панель управления</p>
                </a>
            </li>
            @if(in_array('users', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ $current_route_name == 'users' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user"></i>
                    <p>Пользователи</p>
                </a>
            </li>
            @endif
            @if(in_array('companies', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.companies.index') }}" class="nav-link  {{ $current_route_name == 'companies' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-building"></i>
                    <p>Компании 
                    @if($new_merchants > 0)
                        <span class="rounded-circle bg-warning d-inline-block text-center ml-2" style="width: 21px;height:21px;color: #fff!important;font-size: 14px;float: right;margin-top: 1%;">
                            {{ $new_merchants }}
                        </span>
                    @endif
                    </p>
                </a>
            </li>
            @endif
            @if(in_array('cars', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.cars.index') }}" class="nav-link  {{ $current_route_name == 'cars' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>Водители
                        @if($new_cars > 0)
                            <span class="rounded-circle bg-warning d-inline-block text-center ml-2" style="width: 21px;height:21px;color: #fff!important;font-size: 14px;">
                                {{ $new_cars }}
                            </span>
                        @endif
                    </p>

                </a>
            </li>
            @endif
            @if(in_array('bookings', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.bookings.index') }}" class="nav-link  {{ $current_route_name == 'bookings' && request()->segment(2) != 'driver-price-offers' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>Заказы</p>
                </a>
            </li>
            @endif
    {{--        @if(in_array('driver_price_offers', $user_permissions))--}}
    {{--        <li class="nav-item">--}}
    {{--            <a href="{{ route('admin.bookings.driverPriceOffers') }}" class="nav-link  {{ $current_route_name == 'bookings' && request()->segment(2) == 'driver-price-offers' ? 'active': '' }}">--}}
    {{--                <i class="nav-icon fas fa-dollar-sign"></i>--}}
    {{--                <p>{{trans('admin.admin_permissions.driver_price_offers')}}</p>--}}
    {{--            </a>--}}
    {{--        </li>--}}
    {{--        @endif--}}
            @if(in_array('chat', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.chat.index') }}" class="nav-link  {{ $current_route_name == 'chat' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-comments"></i>
                    <p>Чат
                        @if($newMessages > 0)
                            <span class="rounded-circle bg-warning d-inline-block text-center ml-2" style="width: 21px;height:21px;color: #fff!important;font-size: 14px">
                                {{ $newMessages }}
                            </span>
                        @endif
                    </p>
                </a>
            </li>
            @endif

            @if(in_array('transactions', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.transactions.index') }}" class="nav-link  {{ $current_route_name == 'transactions' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-invoice-dollar"></i>
                    <p>Транзакции </p>
                </a>
            </li>
            @endif

            @if(in_array('rates_history', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.rate-history.index') }}" class="nav-link  {{ $current_route_name == 'rate-history' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-history"></i>
                    <p>@lang('admin.admin_permissions.rates_history') </p>
                </a>
            </li>
            @endif

            @if(in_array('tracking', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.tracking.index') }}" class="nav-link  {{ $current_route_name == 'tracking' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-truck"></i>
                    <p>Трекинг</p>
                </a>
            </li>
            @endif

            @if(in_array('admins', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.admins.index') }}" class="nav-link  {{ $current_route_name == 'admins' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Роли пользователей </p>
                </a>
            </li>
            @endif

            @if(
        in_array('load-types', $user_permissions)
        || in_array('seasons', $user_permissions)
        || in_array('car-types', $user_permissions)
        || in_array('sizes', $user_permissions)
        || in_array('commission-rates', $user_permissions)
        || in_array('cargo-types', $user_permissions)
        || in_array('regions', $user_permissions)
        || in_array('cancel_reasons', $user_permissions)
        || in_array('ticket_themes', $user_permissions)
    )
            <li class="nav-item {{ ($current_route_name == 'regions' ||
            $current_route_name == 'load-types' ||
            $current_route_name == 'seasons' ||
            $current_route_name == 'car-types' ||
            $current_route_name == 'sizes' ||
            $current_route_name == 'commission-rates' ||
            $current_route_name == 'cancel-reasons' ||
            $current_route_name == 'ticket-themes' ||
            $current_route_name == 'cargo-types') ? 'menu-is-opening menu-open' : '' }}">
                <a href="#" class="nav-link {{ ($current_route_name == 'regions' ||
            $current_route_name == 'load-types' ||
            $current_route_name == 'seasons' ||
            $current_route_name == 'car-types' ||
            $current_route_name == 'commission-rates' ||
            $current_route_name == 'sizes' ||
            $current_route_name == 'cancel-reasons' ||
            $current_route_name == 'ticket-themes' ||
            $current_route_name == 'company_priorities' ||
            $current_route_name == 'cargo-types') ? 'menu-is-opening menu-open' : '' }}">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        Справочники
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" {{ ($current_route_name == 'regions' || $current_route_name == 'seasons' || $current_route_name == 'load-types' || $current_route_name == 'car-types' || $current_route_name == 'sizes' || $current_route_name == 'commission-rates' || $current_route_name == 'cargo-types') ? 'style="display: block;"' : '' }}>

                    @if(in_array('seasons', $user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.seasons.index') }}" class="nav-link {{ $current_route_name == 'seasons' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Сезоны</p>
                        </a>
                    </li>
                    @endif
                    @if(in_array('car-types', $user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.regions.changeByPrice') }}" class="nav-link {{ $current_route_name == 'regions' && request()->segment(2) == 'changeByPrice' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Обновление тарифов</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.regions.distances') }}" class="nav-link {{ request()->segment(2) == 'distances' || request()->segment(2) == 'manageTariffs' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Расстоянии</p>
                        </a>
                    </li>
                    @endif
                    @if(in_array('car-types', $user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.car-types.servingPrices') }}" class="nav-link {{ request()->segment(2) == 'serving_prices' ? 'active' : '' }}">
                            <i class="far fa-money-bill-alt nav-icon"></i>
                            <p>Цены подач (Таблица)</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.car-types.index') }}" class="nav-link {{ $current_route_name == 'car-types' && request()->segment(2) != 'serving_prices' && request()->segment(2) != 'serving_prices_partial' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>@lang('admin.nav.car-types')</p>
                        </a>
                    </li>
                        @endif
                        @if(in_array('sizes', $user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.sizes.index') }}" class="nav-link {{ $current_route_name == 'sizes' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>@lang('admin.nav.sizes')</p>
                        </a>
                    </li>
                        @endif

                        @if(in_array('regions', $user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.regions.index') }}" class="nav-link {{ $current_route_name == 'regions' && request()->segment(2) != 'distances' && request()->segment(2) != 'manageTariffs' && request()->segment(2) != 'changeByPrice' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>@lang('admin.nav.regions')</p>
                        </a>
                    </li>
                        @endif



                        @if(in_array('load-types', $user_permissions))
                        <li class="nav-item">
                        <a href="{{ route('admin.load-types.index') }}" class="nav-link {{ $current_route_name == 'load-types' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>@lang('admin.nav.load-types')</p>
                        </a>
                    </li>
                        @endif
                        @if(in_array('cargo-types', $user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.cargo-types.index') }}" class="nav-link {{ $current_route_name == 'cargo-types' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>@lang('admin.nav.cargo-types')</p>
                        </a>
                    </li>
                        @endif
                        @if(in_array('commission-rates', $user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.commission-rates.index') }}" class="nav-link {{ $current_route_name == 'commission-rates' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Процентная ставка</p>
                        </a>
                    </li>
                        @endif
                        @if(in_array('commission-rates', $user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.company_priorities.index') }}" class="nav-link {{ $current_route_name == 'company_priorities' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Приоритеты юр. лиц</p>
                        </a>
                    </li>
                        @endif
                        @if(in_array('cancel_reasons', $user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.cancel-reasons.index') }}" class="nav-link {{ $current_route_name == 'cancel-reasons' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Причины отмены</p>
                        </a>
                    </li>
                        @endif
                        @if(in_array('ticket_themes', $user_permissions))
                    <li class="nav-item">
                        <a href="{{ route('admin.ticket-themes.index') }}" class="nav-link {{ $current_route_name == 'ticket-themes' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Темы предл. и жалоб</p>
                        </a>
                    </li>
                        @endif
                </ul>
            </li>
            @endif

            {{-- @if(in_array('frontend', $user_permissions)) --}}
            @if(in_array('frontend', $user_permissions))
                <li class="nav-item {{ $current_route_name == 'frontend' || $current_route_name == 'app-reviews' ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link  {{ $current_route_name == 'frontend' ? 'active' : '' }}">
                        <i class="nav-icon fab fa-safari"></i>
                        <p>
                            Frontend
                            <i class="fas fa-angle-left right"></i>
                        </p>

                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.frontend.website') }}" class="nav-link pl-4 pr-4 d-block  {{ $current_route_name == 'frontend' && request()->segment(2) == 'website' ? 'active' : '' }}">
                                <i class="nav-icon far fa-window-maximize"></i>
                                <p>Web-сайт </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.frontend.app-reviews') }}" class="nav-link pl-4 pr-4 d-block  {{ $current_route_name == 'frontend' && request()->segment(2) == 'app-reviews' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-info-circle"></i>
                                <p>Отзывы компаний </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.frontend.offer') }}"
                                class="nav-link pl-4 pr-4 d-block  {{ $current_route_name == 'frontend' && request()->segment(2) == 'offer' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file"></i>
                                <p>Оферта </p>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if(in_array('system', $user_permissions))
            <li class="nav-item {{ $current_route_name == 'system' ? 'menu-is-opening menu-open' : '' }}">
                <a href="#" class="nav-link  {{ $current_route_name == 'system' ? 'active' : '' }}">
                    {{-- <i class="nav-icon fab fa-safari"></i> --}}
                    <i class="nav-icon fab fa-ubuntu"></i>
                    <p>
                        Система
                        <i class="fas fa-angle-left right"></i>
                    </p>

                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.system.logs') }}" class="nav-link pl-4 pr-4 d-block  {{ $current_route_name == 'system' && request()->segment(2) == 'logs' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-info-circle"></i>
                            <p>Логи системы </p>
                        </a>
                        @if (in_array('confirmation-codes', $user_permissions))
                            <a href="{{ route('admin.system.confirmationCodes') }}" class="nav-link pl-4 pr-4 d-block  {{ $current_route_name == 'system' && request()->segment(2) == 'confirmation-codes' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-comment-dots"></i>
                                <p>Коды подтверждения </p>
                            </a>
                        @endif
                    </li>
                </ul>
            </li>
            @endif

            @if(in_array('statistics', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.statistics.index') }}" class="nav-link sidebar-nav-link {{ $current_route_name == 'statistics' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chart-pie"></i>
                    <p class="sidebar-nav-label">
                        <span class="sidebar-nav-text">Статистика/Аналитика</span>
                    </p>
                </a>
            </li>
            @endif

            @if(in_array('statistics', $user_permissions))
            <li class="nav-item {{ $current_route_name == 'dashboards' ? 'menu-is-opening menu-open' : '' }}">
                <a href="#" class="nav-link sidebar-nav-link {{ $current_route_name == 'dashboards' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <p class="sidebar-nav-label">
                        <span class="sidebar-nav-text">Дашборды</span>
                        <i class="fas fa-angle-left right sidebar-nav-arrow"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview sidebar-nav-treeview" {{ $current_route_name == 'dashboards' ? 'style="display: block;"' : '' }}>
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboards.gmv') }}" class="nav-link sidebar-nav-link sidebar-nav-link--sub {{ request()->segment(1) == 'dashboards' && request()->segment(2) != 'funnel' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p class="sidebar-nav-label">
                                <span class="sidebar-nav-text">GMV</span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboards.funnel') }}" class="nav-link sidebar-nav-link sidebar-nav-link--sub {{ request()->segment(1) == 'dashboards' && request()->segment(2) == 'funnel' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p class="sidebar-nav-label">
                                <span class="sidebar-nav-text">Funnel</span>
                            </p>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if(in_array('reviews', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.reviews.index') }}" class="nav-link sidebar-nav-link {{ $current_route_name == 'reviews' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-headset"></i>
                    <p class="sidebar-nav-label">
                        <span class="sidebar-nav-text">Поддержка/Отзывы</span>
                    </p>
                </a>
            </li>
            @endif

            @if(in_array('tickets', $user_permissions))
                <li class="nav-item">
                    <a href="{{ route('admin.tickets.index') }}" class="nav-link sidebar-nav-link {{ $current_route_name == 'tickets' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p class="sidebar-nav-label">
                            <span class="sidebar-nav-text">Предл. и жалобы</span>
                        </p>
                    </a>
                </li>
            @endif

            @if(in_array('articles', $user_permissions))
                <li class="nav-item">
                    <a href="{{ route('admin.articles.index') }}" class="nav-link sidebar-nav-link {{ $current_route_name == 'articles' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-newspaper"></i>
                        <p class="sidebar-nav-label">
                            <span class="sidebar-nav-text">Новости</span>
                        </p>
                    </a>
                </li>
            @endif

            @if(in_array('contacts', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.contacts.index') }}" class="nav-link sidebar-nav-link {{ $current_route_name == 'contacts' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-cog"></i>
                    <p class="sidebar-nav-label">
                        <span class="sidebar-nav-text">Настройки</span>
                    </p>
                </a>
            </li>
            @endif
            @if(in_array('appversion', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.appversions.index') }}" class="nav-link sidebar-nav-link {{ $current_route_name == 'appversions' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-mobile"></i>
                    <p class="sidebar-nav-label">
                        <span class="sidebar-nav-text">Версии приложений</span>
                    </p>
                </a>
            </li>
            @endif
            <!-- @if(in_array('merchanthelprequests', $user_permissions))
            <li class="nav-item">
                <a href="{{ route('admin.merchanthelprequests.index') }}" class="nav-link  {{ $current_route_name == 'merchanthelprequests'? 'active': '' }}">
                    <i class="nav-icon fas fa-ticket-alt"></i>
                    <p>Запросы 
                    @if($merchant_help_count > 0)
                        <span class="rounded-circle bg-warning d-inline-block text-center ml-2" style="width: 21px;height:21px;color: #fff!important;font-size: 14px;float: right;margin-top: 1%;">
                            {{ $merchant_help_count > 9 ? '9+': $merchant_help_count }}
                        </span>
                    @endif
                    </p>
                </a>
            </li>
            @endif -->

            @if(in_array('cars', $user_permissions))
            <li class="nav-item {{ in_array($current_route_name, ['merchanthelprequests', 'deleterequests']) ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link sidebar-nav-link {{ in_array($current_route_name, ['merchanthelprequests', 'deleterequests']) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-ticket-alt"></i>
                    <p class="sidebar-nav-label">
                        <span class="sidebar-nav-text">Запросы</span>
                        @if($merchant_help_count > 0 || $user_delete_request_count > 0)
                            <span class="sidebar-counter">
                                {{ $merchant_help_count + $user_delete_request_count }}
                            </span>
                        @endif
                        <i class="fas fa-angle-left right sidebar-nav-arrow"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview sidebar-nav-treeview" {{ in_array($current_route_name, ['merchanthelprequests', 'deleterequests']) ? 'style="display: block;"' : '' }}>

                    <li class="nav-item">
                        <a href="{{ route('admin.merchanthelprequests.index') }}" class="nav-link sidebar-nav-link sidebar-nav-link--sub {{ $current_route_name == 'merchanthelprequests' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p class="sidebar-nav-label">
                                <span class="sidebar-nav-text">На подключение</span>
                            @if($merchant_help_count > 0)
                                <span class="sidebar-counter">
                                    {{ $merchant_help_count }}
                                </span>
                            @endif
                            </p>
                        </a>
                    </li>

                    @if(in_array('admins', $user_permissions))
                        <li class="nav-item">
                            <a href="{{ route('admin.deleterequests.index') }}" class="nav-link sidebar-nav-link sidebar-nav-link--sub {{ $current_route_name == 'deleterequests' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-minus-circle"></i>
                                <p class="sidebar-nav-label">
                                    <span class="sidebar-nav-text">На удаление</span>
                                @if($user_delete_request_count > 0)
                                    <span class="sidebar-counter">
                                        {{ $user_delete_request_count }}
                                    </span>
                                @endif
                                </p>
                            </a>
                        </li>
                    @endif

                </ul>
            </li>
            @endif
        </ul>
@endsection
