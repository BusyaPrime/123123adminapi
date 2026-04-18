@extends('ui.top-bar')

@section('top-bar-left')@endsection
@section('top-bar-right')
{{--    <li class="nav-item">--}}
{{--        <a class="nav-link" data-widget="navbar-search" href="#" role="button">--}}
{{--            <img src="{{ asset('casva/dist/img/search-icon.png') }}" alt="">--}}
{{--        </a>--}}
{{--        <div class="navbar-search-block navbar-search-open" style="max-width: 250px;position: relative;--}}
{{--    float: right;height: 100%;">--}}
{{--            <form class="form-inline">--}}
{{--                <div class="input-group input-group-sm">--}}
{{--                    <input class="form-control form-control-navbar" type="search" placeholder="Поиск" aria-label="Поиск">--}}
{{--                    <div class="input-group-append">--}}
{{--                        <button class="btn btn-navbar" type="submit">--}}
{{--                            <i class="fas fa-search"></i>--}}
{{--                        </button>--}}
{{--                        <button class="btn btn-navbar" type="button" data-widget="navbar-search">--}}
{{--                            <i class="fas fa-times"></i>--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </form>--}}
{{--        </div>--}}
{{--    </li>--}}

{{--    <li class="nav-item dropdown">--}}
{{--        <a class="nav-link" data-toggle="dropdown" href="#">--}}
{{--            <img src="{{ asset('casva/dist/img/notif-icon.png') }}" height="18" alt="Notifications">--}}
{{--            <span class="badge badge-warning navbar-badge">15</span>--}}
{{--        </a>--}}
{{--        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">--}}
{{--            <span class="dropdown-item dropdown-header">Нет уведомлений</span>--}}
{{--            <div class="dropdown-divider"></div>--}}
{{--            <a href="#" class="dropdown-item">--}}
{{--                <i class="fas fa-envelope mr-2"></i> 4 new messages--}}
{{--                <span class="float-right text-muted text-sm">3 mins</span>--}}
{{--            </a>--}}
{{--            <div class="dropdown-divider"></div>--}}
{{--            <a href="#" class="dropdown-item">--}}
{{--                <i class="fas fa-users mr-2"></i> 8 friend requests--}}
{{--                <span class="float-right text-muted text-sm">12 hours</span>--}}
{{--            </a>--}}
{{--            <div class="dropdown-divider"></div>--}}
{{--            <a href="#" class="dropdown-item">--}}
{{--                <i class="fas fa-file mr-2"></i> 3 new reports--}}
{{--                <span class="float-right text-muted text-sm">2 days</span>--}}
{{--            </a>--}}
{{--            <div class="dropdown-divider"></div>--}}
{{--            <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>--}}
{{--        </div>--}}
{{--    </li>--}}

    <li class="nav-item">
        <div class="dropdown nav-link">
            <a href="#" class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{ asset('casva/dist/img/user-icon.png') }}" alt="User">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('admin.profile.edit')}}" class="dropdown-item"><i class="dropdown-icon icmn-user"></i> @lang('admin.profile')</a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('admin.auth.logout') }}" class="dropdown-item text-danger"><i
                        class="dropdown-icon icmn-exit"></i> @lang('admin.logout')</a>
            </div>
        </div>
    </li>
@endsection
{{--@push('styles')--}}
{{--    <style>--}}
{{--        .dropdown-no-arrow::after {--}}
{{--            display: none!important;--}}
{{--        }--}}
{{--    </style>--}}
{{--@endpush--}}
