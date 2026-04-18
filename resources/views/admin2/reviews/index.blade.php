@extends('admin2.layout')

@section('center_content')
    @component('component.card', ['title' => 'Отзывы', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')
            <div class="row col-sm-6 justify-content-end">
                @if($filters['id'] != '' || $filters['user_phone'] != '' || $filters['driver_phone'] != '')
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
            <section class="content" id="filter_block" style="{{ $filters['id'] != '' || $filters['user_phone'] != '' || $filters['driver_phone'] != '' ?'':'display: none' }}">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="{{ route('admin2.reviews.index') }}" id="filter_block_form">
                                <div class="border rounded p-3">
                                        <div class="row justify-content-between">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="id" value="{{ $filters['id'] ?? '' }}" class="form-control" placeholder="ID заказа">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="user_phone" value="{{ $filters['user_phone'] ?? '' }}" class="form-control" placeholder="Номер клиента">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="driver_phone" value="{{ $filters['driver_phone'] ?? '' }}" class="form-control" placeholder="Номер водителя">
                                                </div>
                                            </div>


                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="sort">
                                                        <option value="-id" {{ $filters['sort'] == '-id'? 'selected':'' }}>Сначала новые</option>
                                                        <option value="id" {{ $filters['sort'] == 'id'? 'selected':'' }}>Сначала старые</option>
                                                        <option value="-rating" {{ $filters['sort'] == '-rating'? 'selected':'' }}>Сначала высокие оценки от клиента</option>
                                                        <option value="rating" {{ $filters['sort'] == 'rating'? 'selected':'' }}>Сначала низкие оценки от клиента</option>
                                                        <option value="-driver_rating" {{ $filters['sort'] == '-driver_rating'? 'selected':'' }}>Сначала высокие оценки от водителя</option>
                                                        <option value="driver_rating" {{ $filters['sort'] == 'driver_rating'? 'selected':'' }}>Сначала низкие оценки от водителя</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="row align-items-center justify-content-end">
                                        <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                            <a href="{{ route('admin2.reviews.index') }}" class="btn btn-danger mr-2" style="{{ $filters['id'] != '' || $filters['user_phone'] != '' || $filters['driver_phone'] != '' }};">Сбросить фильтры</a>
                                            <a onclick="$('#filter_block_form').submit();" class="btn btn-info">Применить</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </section>
        @endslot

        @if($reviews->isNotEmpty())
            @foreach($reviews as $review)
                <div class="row border rounded justify-content-center mb-4" style="border-color: #858585!important;">
                    <div class="col-sm-12 d-flex justify-content-between border-bottom" style="border-color: #C4C4C4!important;">
                        <div class="p-2 text-sm text-center col-sm-3 border-right font-weight-bold" style="border-color: #C4C4C4!important;">Детали заказ №{{ $review->id }}</div>
                        <div class="p-2 text-sm text-center col-sm-3 border-right" style="border-color: #C4C4C4!important;">
                            {{ $regionTitles[$review->region_from_id] ?? 'Регион не определен' }} &#10141 {{ $regionTitles[$review->region_to_id] ?? 'Регион не определен' }}</div>
                        <div class="p-2 text-sm text-center col-sm border-right" style="border-color: #C4C4C4!important;">
                            {{ $review->created_at ? $review->created_at->format("d.m.Y"): '--' }}
                        </div>
                        <div class="p-2 text-sm text-center col-sm-2 border-right" style="border-color: #C4C4C4!important;">{{ $review->cargoType->title ?? 'Тип не указан' }}</div>
                        <div class="p-2 text-sm text-center col-sm-2 border-right" style="border-color: #C4C4C4!important;">{{ $review->price ?? 0}} сум</div>
                        <div class="p-2 text-sm text-center col-sm" >
                        </div>
                    </div>
                    <div class="col-sm-12 row">
                        <div class="col-sm-6 p-3 border-right" style="border-color: #C4C4C4!important;">
                            <div class="col-sm-12 d-flex border rounded justify-content-between p-2" style="border-color: #C4C4C4!important;">
                                <div class="d-flex justify-content-between ">
                                    <div class="align-self-center mr-3">
                                        <img src="{{ isset($review->user) ? $review->user->imageUrl(): asset('uploads/defaults/user.png') }}" alt="" style="width: 75px">
                                    </div>
                                    <div class="text-sm mr-3">
                                        <p class="mb-2">
                                            {{ ($review->user->surname ?? '').' '.($review->user->name ?? '').' '.($review->user->middle_name ?? '') }}
                                        </p>
                                        <p class="mb-2"><span>ID:  {{ $review->user_id ?? '' }}</span><span class="pl-3">Телефон:  {{ $review->user->user->username ?? '' }}</span></p>
                                        <p class="mb-2 d-flex align-items-center">
                                            <i class="fas fa-star text-warning mr-2"></i>
                                            <span> {{ $review->rating > 0? $review->rating:'--'}}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="mr-3 d-flex flex-column justify-content-between">

                                </div>
                            </div>
                            <h5 class="mt-3">Отзыв</h5>
                            <div class="text-sm ml-3" style="line-height: 1.4;">
                                {{ $review->review ?? 'Нет отзыва' }}
                            </div>
                        </div>
                        <div class="col-sm-6 p-3">
                            <div class="col-sm-12 d-flex border rounded justify-content-between p-2" style="border-color: #C4C4C4!important;">
                                <div class="d-flex justify-content-between ">
                                    <div class="align-self-center mr-3">
                                        <img class="rounded-circle" src="{{ isset($review->driver) ? $review->driver->imageUrl(): asset('uploads/defaults/user.png') }}" alt="" style="width: 75px">
                                    </div>
                                    <div class="text-sm mr-3">
                                        <p class="mb-2">{!! $review->driver_id ? (($review->driver->surname ?? '').' '.($review->driver->name ?? '').' '.($review->driver->middle_name ?? '')) : '' !!}
                                            ({!! $review->company ? $review->company->title: 'Самозанятый' !!})
                                        </p>
                                        <p class="mb-2"><span>ID:  {{ $review->driver_id ?? '' }}</span><span class="pl-3">Телефон:  {{ $review->driver->user->username ?? '' }}</span></p>
                                        <p class="mb-2 d-flex align-items-center">
                                            <i class="fas fa-star text-warning mr-2"></i>
                                            <span> {{ $review->driver_rating > 0? $review->driver_rating:'--'}}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="mr-3 d-flex flex-column justify-content-between">
                                </div>
                            </div>
                            <h5 class="mt-3">Ответ</h5>
                            <div class="text-sm ml-3" style="line-height: 1.4;">
                                {{ $review->driver_review ?? 'Нет ответа' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        @slot('bottom')
            @include('ui.pagination', ['data' => $reviews])
        @endslot
    @endcomponent
@endsection
