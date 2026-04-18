@extends('admin2.layout')

@section('center_content')

    @component('component.card', [ 'bodyClass' => 'card-body-no-padding'])

{{--        @slot('buttons')--}}
{{--            <div class="col-sm-6 d-flex align-items-end justify-content-end">--}}
{{--                <div class="balanse mb-0 mr-2 border rounded-pill px-3 py-2">--}}
{{--                    <span class="text-primary" style="font-weight: bold;">Баланс компании</span>--}}
{{--                    <span class="text-primary ml-4" style="font-size: 25px;">{{ $company->balance ?? 0 }} сум</span>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endslot--}}

        <div class="row">
            <div class="col-sm-3">
                <div class="left-bar d-flex flex-column align-items-center border rounded-lg p-3 pb-3" style="background-color: #002C47;">
                    <div class="px-5 pt-3 mb-2">
                        <img src="{{ $company->logo ? $company->imageUrl('logo'): asset('uploads/defaults/company.png') }}" class="img-fluid img-thumbnail rounded-circle" alt="">
                    </div>
                    <div class="text-white text-center  mb-3">
                        <span class="text-uppercase">{{ $company->title ?? '--' }}</span>
                        <small class="text-white d-block  ">
                            Статус компании:
                            {!! $company->active? '<span class="text-success ">'.trans('admin.active').'</span>': '<span class="text-danger ">'.trans('admin.not_active').'</span>' !!}
                        </small>
                    </div>
					@if($company->user->is_external == 0)
                    <div class="text-center mb-3">
                        <span class="text-white d-block" style="font-size: 14px;">Количество заработанных средств</span>
                        <div class="text-green">{{ $companyBookingStats->done_price_sum ?? 0 }} сум
                            <a href="{{ route('admin2.bookings.index', ['company_id' => $company->id]) }}" class="text-white ml-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        <span class="text-white d-block" style="font-size: 14px;">Сумма отчислений</span>
                        <div class="text-green">{{ $companyBookingStats->done_commission_sum ?? 0 }} сум
                            <a href="{{ route('admin2.transactions.index', ['company_id' => $company->id]) }}" class="text-white ml-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
					@endif
                    <p class="text-white">
                        <i class="fas fa-star text-warning mr-2"></i>
                        <span> {{ $company->rating > 0? $company->rating: '--'}}</span>
                    </p>
                </div>
                @if($company->user->is_external == 0)
                    <div class="text-primary text-center mt-4 mb-2" style="font-size: 21px;">
                        Заказы
                    </div>

                    <div class="border rounded-lg px-3 py-1">
                        <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">Завершенные
                            <span class="text-green" style="font-size: 35px;">{{ $companyBookingStats->done_count ?? 0 }}</span>
                            <a href="{{ route('admin2.bookings.index', ['company_id' => $company->id, 'status' => 'done']) }}" class="text-secondary ml-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>

                    <div class="border rounded-lg mt-3  px-3 py-1">
                        <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">Выполняемые
                            <span class="text-warning" style="font-size: 35px;">{{ $companyBookingStats->processing_count ?? 0 }}</span>
                            <a href="{{ route('admin2.bookings.index', ['company_id' => $company->id, 'status' => 'in_progress']) }}" class="text-secondary ml-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                @endif
                <div class="text-primary text-center mt-4 mb-2" style="font-size: 21px;">
                    @if($company->user->is_external == 1) @lang('admin.users') @else Водители компании @endif
                </div>

                <div class="border rounded-lg px-3 py-1">
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">Количество
                        
						@if($company->user->is_external == 1)
						<span class="text-green" style="font-size: 35px;">{{ $userscount ?? 0 }}</span>
                        <a href="{{ route('admin2.users.index') }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
						@else
						<span class="text-green" style="font-size: 35px;">{{ $company->users_count ?? 0 }}</span>
						<a href="{{ route('admin2.cars.index', ['company_id' => $company->id]) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
						@endif
                    </div>
                </div>


            </div>


            <div class="col-sm-9">
                <form class="border rounded-lg px-3 py-2">
                    <div class="text-primary font-weight-bold mb-2">
                        Информация
                    </div>

                    <div class="row">
                        <!-- <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" style="background: transparent" placeholder="ID компании: {{$company->id ?? ''}}" disabled>
                            </div>
                        </div> -->

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Ф.И.О руководителя: {{($user->profile->surname ?? '').' '.($user->profile->name ?? '').' '.($user->profile->middle_name ?? '')}}" disabled style="background: transparent">
                            </div>
                        </div>

                        @if($company->user->is_external == 0)
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Количество транспорта компании: {{ $company->users_count ?? 0 }}" disabled style="background: transparent">
                                </div>
                            </div>
                        @endif
						@if($company->user->is_external == 0)
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Процентная ставка: {{$company->commissionRate ? $company->commissionRate->title." (".($company->commissionRate->commission ?? 0)."%)": '--'}}" disabled style="background: transparent">
                            </div>
                        </div>
						@endif
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Дата регистрации компании: {{$company->created_at ?$company->created_at->format('d.m.Y'): '--'}}" disabled style="background: transparent">
                            </div>
                        </div>


                    </div>


                    <div class="text-primary font-weight-bold mb-2">
                        Документы компании
                    </div>


                    <div class="row">
                        @if($company->user->is_external == 0)
                            <div class="col-sm-6">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Гувохнома{{ $company->certificate ? '':': нет' }}" disabled style="background: transparent">
                                    @if($company->certificate)
                                        <div class="input-group-append">
                                            <a href="{{ $company->imageUrl('certificate') }}" target="_blank" class="text-secondary bg-transparent input-group-text">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($company->user->is_external == 0)
                        <div class="col-sm-6">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Лицензия{{ $company->licence ? '':': нет' }}" disabled style="background: transparent">
                                    @if($company->licence)
                                        <div class="input-group-append">
                                            <a href="{{ $company->imageUrl('licence') }}" target="_blank" class="text-secondary bg-transparent input-group-text">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="col-sm-6">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Договор о сотрудничестве{{ $company->agreement ? '':': нет' }}" disabled style="background: transparent">
                                @if($company->agreement)
                                    <div class="input-group-append">
                                        <a href="{{ $company->imageUrl('agreement') }}" target="_blank" class="text-secondary bg-transparent input-group-text">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="text-primary font-weight-bold mb-2">
                        Банковские реквизиты
                    </div>


                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Расчетный счет компании: {{$company->bank_account ?? '--'}}" disabled style="background: transparent">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Наименование банка компании: {{$company->bank ?? '--'}}" disabled style="background: transparent">
                            </div>
                        </div>
                    </div>


                    <div class="text-primary font-weight-bold mb-2">
                        Юридические данные
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="МФО компании: {{$company->mfo ?? '--'}}" disabled style="background: transparent">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="ИНН компании: {{$company->inn ?? '--'}}" disabled style="background: transparent">
                            </div>
                        </div>

                        @if($company->user->is_external == 0)
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="ОКЭД компании: {{$company->oked ?? '--'}}" disabled style="background: transparent">
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="text-primary font-weight-bold mb-2">
                        Контакты компании
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Фактический адрес компании: {{$company->address ?? '--'}}" disabled style="background: transparent">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Юридический адрес компании: {{$company->company_address ?? '--'}}" disabled style="background: transparent">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                @if($company->user->is_external == 0) <input type="text" class="form-control" placeholder="Номер телефона: {{ $user->username ?? '' }}
                                        " disabled style="background: transparent">

                                @else <input type="text" class="form-control" placeholder="Номер телефона: {{ $user->profile->phone_number ?? '' }}
                                " disabled style="background: transparent">
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Email ответсвенного лица: {{ $user->email ?? '' }}
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

                </form>
            </div>
        </div>
    @endcomponent
@endsection
