@extends('admin2.layout')

@section('center_content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1>Добро пожаловать в {{ $company->title ?? '--' }}</h1>
                </div>
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
				@if(isset($user) && $user->is_external ==1)
					<style>
					.stat__box {
						padding: 1rem 2rem;
						flex-wrap: wrap;
						height: auto;
					}

					.stat__box img {
						width: 60px;
						object-fit: contain;
						height: 60px;
						margin-right: 2rem;
					}

					p.stat__number {
						margin: 5px 0 0;
					font-size: 1.5rem;
					font-weight: 500;
					width: 100%;
					float: left;
					line-height: 21px;
					color: #d0cece;
									}
									
				h3.stat__name {
					width: auto;
					float: left;
					font-size: 1rem;
					font-weight: 500;
					color: #d0cece;
				}

				a.stat__more {
					font-size: 1rem;
					font-weight: 500;
					color: #d0cece;
				}
									</style>
					<div class="col-lg-6 col-sm-12">
                        <div class="stat__box">
                            <img src="{{ asset('casva/dist/img/widget-9.png') }}" alt="1">
                            <div class="stat__text">
                                <h3 class="stat__name">@lang('admin.number_users')</h3>
                                <p class="stat__number">{{ $userscount ?? 0 }}</p>
                            </div>
                            <a href="{{ route('admin2.users.index') }}" class="stat__more">Подробнее</a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="stat__box">
                            <img src="{{ asset('casva/dist/img/widget-3.png') }}" alt="1">
                            <div class="stat__text">
                                <h3 class="stat__name">Количество заказов</h3>
                                <p class="stat__number">{{ $bookings_count ?? 0 }}</p>
                            </div>
                            @if(isset($user) && $user->is_external == 0)
                                <a href="{{ route('admin2.bookings.index', ['company_id' => $company->id]) }}" class="stat__more">Подробнее</a>
                            @else
                                <a href="{{ route('admin2.bookings.index') }}" class="stat__more">Подробнее</a>
                            @endif
                        </div>
                    </div>
				@else
                    <div class="col-lg-3 col-6">
                        <div class="stat__box">
                            <img src="{{ asset('casva/dist/img/widget-2.png') }}" alt="1">
                            <div class="stat__text">
                                <h3 class="stat__name">Количество водителей</h3>
                                <p class="stat__number">{{ $company->users_count ?? 0 }}</p>
                            </div>
                            <a href="{{ route('admin2.cars.index', ['company_id' => $company->id]) }}" class="stat__more">Подробнее</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="stat__box">
                            <img src="{{ asset('casva/dist/img/widget-3.png') }}" alt="1">
                            <div class="stat__text">
                                <h3 class="stat__name">Количество заказов</h3>
                                <p class="stat__number">{{ $bookings_count ?? 0 }}</p>
                            </div>
                            
                            <a href="{{ route('admin2.bookings.index', ['company_id' => $company->id]) }}" class="stat__more">Подробнее</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="stat__box">
                            <img src="{{ asset('casva/dist/img/widget-5.png') }}" alt="1">
                            <div class="stat__text">
                                <h3 class="stat__name" style="width: 130px">Сумма заработанных средств</h3>
                                <p class="stat__number">{{ $companyBookingStats->done_price_sum ?? 0 }}</p>
                            </div>
                            <a href="{{ route('admin2.bookings.index', ['company_id' => $company->id]) }}" class="stat__more">Подробнее</a>
                        </div>
                    </div>
                <div class="col-lg-3 col-6">
                    <div class="stat__box">
                        <img src="{{ asset('casva/dist/img/widget-6.png') }}" alt="1">
                        <div class="stat__text">
                            <h3 class="stat__name">Сумма отчислений агрегатору</h3>
                            <p class="stat__number">{{ $companyBookingStats->done_commission_sum ?? 0 }}</p>
                        </div>
                        <a href="{{ route('admin2.transactions.index', ['company_id' => $company->id]) }}" class="stat__more">Подробнее</a>
                    </div>
                </div>
				@endif
            </div>
            <!-- /.row -->
            <!-- Main row -->

                <div class="row ways">
                    <div class="col-md-12 ">
                        <div class="ways__name mb-4" style="color: #2b2b2b!important;font-weight: normal!important;">Заказы</div>
                    </div>
					@if(isset($user) && $user->is_external ==0)
                    <div class="col-md-3">
					@else
					<div class="col-md-4">
					@endif
                        <div class="ways__box">
                            <div class="ways__name">Ожидающие</div>
							@if(isset($user) && $user->is_external == 0)
                            <div class="ways__num">{{ $companyBookingStats->waiting_count ?? 0 }}</div>
							@else
							<div class="ways__num">{{ $waitingOrders ?? 0 }}</div>
							@endif
							@if(isset($user) && $user->is_external == 0)
                            <a href="{{ route('admin2.bookings-available.index') }}" class="ways__btn">Подробнее</a>
							@else
							<a href="{{ route('admin2.bookings-available.index', ['company_id' => $company->id]) }}" class="ways__btn">Подробнее</a>
							@endif
                        </div>
                    </div>
                    @if(isset($user) && $user->is_external ==0)
                    <div class="col-md-3">
					@else
					<div class="col-md-4">
					@endif
                        <div class="ways__box">
                            <div class="ways__name">Выполняются</div>
                            @if(isset($user) && $user->is_external == 0)
                                <div class="ways__num">{{ $companyBookingStats->processing_count ?? 0 }}</div>
                                <a href="{{ route('admin2.bookings.index', ['company_id' => $company->id, 'status' => 'in_progress']) }}" class="ways__btn">Подробнее</a>
                            @else
                                <div class="ways__num">{{ $processingOrders ?? 0 }}</div>
                                <a href="{{ route('admin2.bookings.index', ['status' => 'in_progress']) }}" class="ways__btn">Подробнее</a>
                            @endif
                        </div>
                    </div>
                    @if(isset($user) && $user->is_external ==0)
                    <div class="col-md-3">
					@else
					<div class="col-md-4">
					@endif
                        <div class="ways__box">
                            <div class="ways__name">Выполненные</div>

                            @if(isset($user) && $user->is_external == 0)
                                <div class="ways__num">{{ $companyBookingStats->done_count ?? 0 }}</div>
                                <a href="{{ route('admin2.bookings.index', ['company_id' => $company->id, 'status' => 'done']) }}" class="ways__btn">Подробнее</a>
                            @else
                                <div class="ways__num">{{ $doneOrders }}</div>
                                <a href="{{ route('admin2.bookings.index', ['status' => 'done']) }}" class="ways__btn">Подробнее</a>
                            @endif
                        </div>
                    </div>
					@if(isset($user) && $user->is_external ==0)
                    <div class="col-md-3">
                        <div class="ways__box">
                            <div class="ways__name">Отмены</div>
                            <div class="ways__num">{{ $companyBookingStats->canceled_count ?? 0 }}</div>
                            <a href="{{ route('admin2.bookings.index', ['status' => 'canceled']) }}" class="ways__btn">Подробнее</a>
                        </div>
                    </div>
					@endif
                </div>
        <!-- /.row (main row) -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection
