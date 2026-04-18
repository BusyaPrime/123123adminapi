@extends('admin2.layout')

@section('center_content')
    @component('component.card', ['title' => 'Баланс компании / Транзакции', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')

        @endslot

        
        <div class="row">
            @php
                $companyLimitName = $companyLimit->name ?? 'Не назначен';
                $companyLimitQuantity = (int) ($companyLimit->quantity ?? 0);
                $hasExceededCompanyLimit = (-1 * (int) $company->balance) > $companyLimitQuantity;
            @endphp
            <div class="col-sm-3">
                <div class="left-bar d-flex flex-column align-items-center border rounded-lg p-3 pb-3" style="background-color: #002C47;">
                    <div class="px-5 pt-3 mb-2">
                        <img src="{{ $company->logo ? $company->imageUrl('logo'): asset('uploads/defaults/company.png') }}" class="img-fluid img-thumbnail rounded-circle" alt="">
                    </div>
                    <div class="text-white text-center  mb-3">
                        <span class="text-uppercase">{{ $company->title ?? '--' }}</span>
                    </div>
					
                    <p class="text-white">
                        <i class="fas fa-star text-warning mr-2"></i>
                        <span> {{ $company->rating > 0? $company->rating: '--'}}</span>
                    </p>
                    <p class="text-white">
                        <i class="nav-icon fas fa-money-bill"></i>
                        <span class='text-white'>&nbsp;&nbsp;Баланс: </span>
                        <span class='{{ $hasExceededCompanyLimit ? "text-danger" : "text-success" }}'> {{ number_format($company->balance, 0, '', ' ') ?? '0'}} сум</span>
                    </p>
                    <p class="text-white">
                        <i class="nav-icon fas fa-money-bill"></i>
                        <span class='text-white'>&nbsp;&nbsp;Тариф: </span>
                        <span> {{ $companyLimitName }}</span>
                        &nbsp;
                        <span class="info d-inline-block" style="font-size:12px; font-weight:200;cursor:pointer;">
                            <span class="btn text-white" style="pointer-events: none;" type="button"><i class="fas fa-info-circle"></i></span>
                            <span class="info-popover">
                                <b>Ваш тариф: </b><b>{{ $companyLimitName }}</b><br />
                                <b>Порог задолженности: </b><b>{{ number_format($companyLimitQuantity, 0, '', ' ') }} сум</b>
                            </span>
                        </span>
                    </p>
                </div>

                <style>
                    .info{
                        position:relative;
                    }

                    .info:hover .info-popover{
                        visibility: visible;
                        opacity:1;
                    }

                    .info-popover{
                        position:absolute;
                        width:260px;
                        z-index:999;
                        top:0;
                        right:-260px;
                        background: #fff;
                        color:#000;
                        padding:10px 20px;
                        border-radius:10px;
                        box-shadow:0 0 2px #000;
                        font-weight:300;
                        font-size:12.5px;
                        visibility:hidden;
                        opacity:0;
                        transition:.2s all;
                    }

                    .info-popover:before{
                        content:' ';
                        display:block;
                        background:#fff;
                        width:15px;
                        height:15px;
                        position:absolute;
                        top:11px;
                        left:-6px;
                        transform: rotate(-45deg);
                        z-index:8;
                    }
                </style>



            </div>


            <div class="col-sm-9">
                <h4>Транзакции</h4>

                <div class="row">
                
                @if($transactions->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <!-- <th class="table__th">@lang('validation.attributes.id')</th>
                            <th class="table__th">Имя</th> -->
                            <th class="table__th">Компания</th>
                            <th class="table__th">Тип транзакции</th>
                            <th class="table__th">Основание</th>
                            <th class="table__th">Дата</th>
                            <th class="table__th">Сумма</th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($transactions as $transaction)
                        <tr class="table__tr mt-2 mb-2 "  >

                        @php
                            $amount = number_format($transaction->amount, 0, '', ' ');
                        @endphp
                            
                            <td class="table__td">
                                @if($transaction->company_id)
                                    {{ $transaction->company->title ?? '' }}
                                @else
                                    Самозанятый
                                @endif
                            </td>
                            <td class="table__td">{{ $transaction->type == 'refill' ? 'Пополнение': 'Списание' }}</td>
                            <td class="table__td">{{ $transaction->description ?? 'Не указано' }}</td>
                            <td class="table__td">{{ $transaction->created_at->format('d.m.Y H:i') }}</td>
                            <td class="table__td">
                                {!! $transaction->type == 'refill'? '<span class="text-success">+'.$amount.' сум</span>': '<span class="text-danger">-'.$amount.' сум</span>' !!}
                            </td>
                            <td class="table__td" >
                                <div class="dropdown"  >
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin2.transactions.show', $transaction) }}" class="dropdown-item">
                                            Просмотр
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="col-md text-center p-4">
                <div>Нет транзакций</div>
           </div>
                
        @endif

                </div>
            </div>
        </div>

        
    @endcomponent
@endsection
