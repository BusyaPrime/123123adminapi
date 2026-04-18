@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => trans('admin.admin_permissions.driver_price_offers'), 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')
            <div class="row col-sm-6 justify-content-end">
                @if($filters['id'] != '' || $filters['driver_id'] != '' || $filters['amount'] != '' || $filters['booking_id'] != '')
                    <button type="button" class="btn btn-default  ml-2" id="open_filter" style="display: none;">
                        <span class=" ml-2">Фильтр</span>
                        <i class="fas fa-chevron-down text-black pl-2 pr-2" style="color: #2b2b2b!important;"></i>
                    </button>
                    <button type="button" class="btn btn-default  ml-2" id="close_filter" style="">
                        <span class="text-secondary ml-2">Фильтр</span>
                        <i class="fas fa-chevron-up text-black pl-2 pr-2" ></i>
                    </button>
                @else
                    <button type="button" class="btn btn-default  ml-2" id="open_filter">
                        <span class=" ml-2">Фильтр</span>
                        <i class="fas fa-chevron-down text-black pl-2 pr-2" style="color: #2b2b2b!important;"></i>
                    </button>
                    <button type="button" class="btn btn-default  ml-2" id="close_filter" style="display: none;">
                        <span class="text-secondary ml-2">Фильтр</span>
                        <i class="fas fa-chevron-up text-black pl-2 pr-2" ></i>
                    </button>
                @endif
            </div>
        @endslot

        @slot('filters')
            <section class="content" id="filter_block" style="display: {{ ($filters['id'] != '' || $filters['driver_id'] != '' || $filters['amount'] != '' || $filters['booking_id'] != '') ? 'block' : 'none' }}">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="{{ route('admin.bookings.driverPriceOffers') }}" id="filter_block_form">
                                <div class="border rounded p-3">
                                    <div class="form-group">
                                        <div class="row justify-content-between">

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="id" value="{{ $filters['id'] ?? '' }}" class="form-control" placeholder="Поиск по ID Заказа">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="amount" value="{{ $filters['amount'] ?? '' }}" class="form-control" placeholder="Поиск по стоимости">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="driver_id" value="{{ $filters['driver_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID водителя">
                                                </div>
                                            </div>

                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="booking_id" value="{{ $filters['booking_id'] ?? '' }}" class="form-control" placeholder="Поиск по ID заказа">
                                                </div>
                                            </div>
                                        </div>


                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="sort">
                                                        <option value="-price" {{ $filters['sort'] == '-amount'? 'selected':'' }}>Сначала самые дешевые</option>
                                                        <option value="price" {{ $filters['sort'] == 'amount'? 'selected':'' }}>Сначала самые дорогие</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row align-items-center justify-content-end">
                                                <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                    <a href="{{ route('admin.bookings.driverPriceOffers') }}" class="btn btn-danger mr-2" style="{{ $filters['id'] != '' || $filters['driver_id'] != '' || $filters['amount'] != '' || $filters['booking_id'] != '' }};">Сбросить фильтры</a>
                                                    <a onclick="$('#filter_block_form').submit();" class="btn btn-info">Применить</a>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        @endslot

        @if($bookings->isNotEmpty())

                <table class="w-100">
                    <thead class="table__thead text-center">
                        <tr>
                            <th class="table__th">ID заказа</th>
                            <th class="table__th">Кол-во предложений</th>
                            <th class="table__th"></th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($bookings as $booking)
                        <tr class="table__tr mt-2 mb-2 redirect-show-page text-center"  data-url="{{ route('admin.bookings.show', $booking) }}">
                            <td class="table__td">{{ $booking->id }}</td>

                            <td class="table__td">
                                {{ $booking->bookingPriceOffer->count() }}
                            </td>
                            <td class="table__td">
                                show
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
        @endif

        @push('styles')
            <style>
                .hover_container{
                    position: relative;
                }

                .cars-container{
                    position: absolute;
                    right: 10px;
                    top: 12px;
                    border-radius:10px;
                    box-shadow: 0 0 5px rgba(0,0,0,.3);
                    padding:4px;
                    background: #fefefe;
                    z-index: 999;
                    /* display:none; */
                    visibility:hidden;
                    opacity:0;
                    transition: .2s all;
                }
                .car{
                    padding:3px 5px;
                    min-width:250px;
                    border-bottom:1px solid #efefef;
                    margin: 3px 0;
                }
                
                .car:last-child{
                    border:none;
                    margin-bottom:0;
                }

                .car-container{
                    display:flex;
                    align-items:center;
                }
                .car-container .avatar{
                    flex:1 0 30px;
                    width: 30px;
                    max-width: 30px;
                    border-radius: 50%;
                    overflow:hidden;
                }
                
                .car-container .avatar img{
                    width:100%;
                }
                
                .car-container .driver-name{
                    flex:1 0;
                    padding: 5px;
                    font-size:13px;
                    line-height:12px;
                }
                
                .car-container .time{
                    flex:0 0 80px;
                    width:80px;
                    padding: 0 10px;
                    font-size:11px;
                    line-height:11px;
                }

                .hover_container:hover .cars-container{
                    visibility: visible;
                    opacity: 1;
                }
                

            </style>
        @endpush

        @slot('bottom')
            @include('ui.pagination', ['data' => $bookings])
        @endslot
        @endcomponent
@endsection
