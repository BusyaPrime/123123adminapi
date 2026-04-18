@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Подробнее о компании', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')
            <div class="col-sm-6 d-flex align-items-end justify-content-end">
                <div class="balanse mb-0 mr-2 border rounded-pill px-3 py-2">
                    <span class="text-primary" style="font-weight: bold;">Баланс компании</span>
                    <span class="text-primary ml-4" style="font-size: 25px;">{{ number_format($company->balance, 0, '', ' ') ?? 0 }} сум</span>
                </div>
            </div>
        @endslot

        <div class="row">
            <div class="col-sm-3">
                <div class="left-bar d-flex flex-column align-items-center border rounded-lg p-3 pb-3" style="background-color: #002C47;">
                    <div class="px-5 pt-3 mb-2" style='width:200px; height:200px; background-image: url("{{ $company->logo ? $company->imageUrl("logo"): asset("uploads/defaults/company.png") }}"); background-position: center center; background-size:cover; overflow:hidden; border-radius:50%; border: 3px solid #fff'>
                    </div>
                    <div class="text-white text-center  mb-3">
                        <span class="text-uppercase">{{ $company->title ?? '--' }}</span>
                        <small class="text-white d-block  ">
                        Статус компании:
                            {!! $company->active ? '<span class="text-success ">'.trans('admin.active').'</span>': '<span class="text-danger ">'.trans('admin.not_active').'</span>' !!}
                        </small>
                    </div>
					@if($company->user->is_external == 0)
                    <div class="text-center mb-3">
                        <span class="text-white d-block" style="font-size: 14px;">Количество заработанных средств</span>
                        <div class="text-green">{{ number_format($companyBookingStats->done_price_sum ?? 0, 0, '', ' ') }} сум
                            <a href="{{ route('admin.bookings.index', ['company_id' => $company->id]) }}" class="text-white ml-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        <span class="text-white d-block" style="font-size: 14px;">Сумма отчислений</span>
                        <div class="text-green">{{ number_format($companyBookingStats->done_commission_sum ?? 0, 0, '', ' ') }} сум
                            <a href="{{ route('admin.transactions.index', ['company_id' => $company->id]) }}" class="text-white ml-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
					@endif
                    <p class="text-white">
                        <i class="fas fa-star text-warning mr-2"></i>
                        <span> {{ $company->rating > 0 ? $company->rating: '--'}}</span>
                    </p>
                </div>
                <div class="text-primary text-center mt-4 mb-2" style="font-size: 21px;">
                    Заказы
                </div>

                @if($company->user->is_external == 1)
                    <div class="border rounded-lg mt-3  px-3 py-1">
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">Новые заказы
                        <span class="text-primary" style="font-size: 35px;">{{ $bookingsCountNew }}</span>
                        <a href="{{ route('admin.bookings.index', $company->user->is_external == 0 ? ['company_id' => $company->id, 'status' => 'order'] : ['client_company_id' => $company->id, 'status' => 'order']) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                @endif

                
                <div class="border rounded-lg mt-3  px-3 py-1">
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">Выполняемые
                        <span class="text-warning" style="font-size: 35px;">{{ $bookingsCountProcessing }}</span>
                        <a href="{{ route('admin.bookings.index', $company->user->is_external == 0 ? ['company_id' => $company->id, 'status' => 'in_progress'] : ['client_company_id' => $company->id, 'status' => 'in_progress']) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>

                <div class="border rounded-lg mt-3 px-3 py-1">
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">Завершенные
                        <span class="text-green" style="font-size: 35px;">{{ $bookingsCountDone }}</span>
                        <a href="{{ route('admin.bookings.index', $company->user->is_external == 0 ? ['company_id' => $company->id, 'status' => 'done'] : ['client_company_id' => $company->id, 'status' => 'done']) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>

                @if($company->user->is_external == 1)
                    <div class="border rounded-lg mt-3  px-3 py-1">
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">Отменённые
                        <span class="text-danger" style="font-size: 35px;">{{ $bookingsCountCancelled }}</span>
                        <a href="{{ route('admin.bookings.index', $company->user->is_external == 0 ? ['company_id' => $company->id, 'status' => 'canceled'] : ['client_company_id' => $company->id, 'status' => 'canceled']) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                @endif
                
                <div class="border rounded-lg mt-3 px-3 py-1">
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">Общее кол-во.
                        <span class="text-primary" style="font-size: 35px;">{{ $bookingsCountNew + $bookingsCountProcessing + $bookingsCountDone + $bookingsCountCancelled }}</span>
                        <a href="{{ route('admin.bookings.index', $company->user->is_external == 0 ? ['company_id' => $company->id] : ['client_company_id' => $company->id]) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>

                <div class="text-primary text-center mt-4 mb-2" style="font-size: 21px;">
                   @if($company->user->is_external == 1) Пользователи @else Водители компании @endif
                </div>

                <div class="border rounded-lg px-3 py-1">
                    <div class="text-primary d-flex align-items-center justify-content-between" style="font-size: 18px;">Количество
                        <span class="text-green" style="font-size: 35px;">{{ $company->users_count ?? 0 }}</span>
						@if($company->user->is_external == 1)
                        <a href="{{ route('admin.users.index', ['company_id' => $company->id]) }}" class="text-secondary ml-2">
                            <i class="fas fa-eye"></i>
                        </a>
						@else
						<a href="{{ route('admin.cars.index', ['company_id' => $company->id]) }}" class="text-secondary ml-2">
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

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Тип компании: {{trans('admin.company_types.'.$company->role)}}</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Ф.И.О руководителя: {{($user->profile->surname ?? '').' '.($user->profile->name ?? '').' '.($user->profile->middle_name ?? '')}}</div>
                            </div>
                        </div>


                        @if($company->user->is_external == 0)
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Количество транспорта компании: {{ $company->users_count ?? 0 }}</div>
                            </div>
                        </div>
                        @else
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Количество пользователей: {{ $company->users_count ?? 0 }}</div>
                            </div>
                        </div>
                        @endif
						@if($company->user->is_external == 0)
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Процентная ставка: {{$company->commissionRate ? $company->commissionRate->title." (".($company->commissionRate->commission ?? 0)."%)": '0%'}}</div>
                            </div>
                        </div>
						@endif
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Дата регистрации компании: {{$company->created_at ?$company->created_at->format('d.m.Y'): 'Нет'}}</div>
                            </div>
                        </div>


                    </div>


                    <div class="text-primary font-weight-bold mb-2">
                        Документы компании
                    </div>


                    <div class="row">
                    {{-- @if($company->user->is_external != 1) --}}
                        {{-- <div class="col-sm-6">
                            <div class="input-group mb-3">
                                <div type="text" class="form-control" placeholder="Гувохнома{{ $company->certificate ? '':': нет' }}" disabled style="background: transparent">
                                @if($company->certificate)
                                <div class="input-group-append">
                                    <a href="{{ $company->imageUrl('certificate') }}" target="_blank" class="text-secondary bg-transparent input-group-text">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div> --}}
                    {{-- @endif --}}

                        {{-- @if($company->user->is_external != 1) --}}
                        {{-- <div class="col-sm-6">
                            <div class="input-group mb-3">
                                <div type="text" class="form-control" placeholder="Лицензия{{ $company->licence ? '':': нет' }}" disabled style="background: transparent">
                                @if($company->licence)
                                    <div class="input-group-append">
                                        <a href="{{ $company->imageUrl('licence') }}" target="_blank" class="text-secondary bg-transparent input-group-text">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div> --}}
                        {{-- @endif --}}
                        <div class="col-sm-6">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Договор о сотрудничестве: {{ $company->agreement ? 'Файл загружен':': нет' }}" disabled style="background: transparent">
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
                                <div type="text" class="form-control rounded">Расчетный счет компании: {{$company->bank_account ?? 'Не задано'}}</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Наименование банка компании: {{$company->bank ?? 'Не задано'}}</div>
                            </div>
                        </div>
                    </div>


                    <div class="text-primary font-weight-bold mb-2">
                        Юридические данные
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">МФО компании: {{$company->mfo ?? 'Не задано'}}</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">ИНН компании: {{$company->inn ?? 'Не задано'}}</div>
                            </div>
                        </div>

                        @if($company->user->is_external != 1) <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">ОКЭД компании: {{$company->oked ?? 'Не задано'}}</div>
                            </div>
                        </div>
                        @endif

                    </div>

                    <div class="text-primary font-weight-bold mb-2">
                        Контакты компании
                    </div>

                    <div class="row">
                    @if($company->user->is_external != 1) <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Фактический адрес компании: {{$company->address ?? 'Не задано'}}</div>
                            </div>
                        </div>
                    @endif

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="rounded">Юридический адрес компании: {{$company->company_address ?? 'Не задано'}}</div>
                            </div>
                        </div>
                        @if($company->user->is_external != 1)
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Номер телефона: +{{ $user->username ?? 'Не задано' }}</div>
                            </div>
                        </div>
                        @else
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Номер телефона: +{{ $user->profile->phone_number ?? 'Не задано' }}</div>
                            </div>
                        </div>
                        @endif

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Email ответсвенного лица: {{ $user->email ?? 'Не задано' }}</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div type="text" class="form-control rounded">Telegram: {{ $user->profile->telegram ?? 'Не задано' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="add-company__2 d-flex align-items-center justify-content-between ">
                        <a href="{{ route('admin.companies.index') }}" class="btn btn-warning my-2">Назад</a>
                        <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-primary my-2 ">Редактировать данные компании</a>
                    </div>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
