@extends('admin2.layout')

@section('center_content')
    @component('component.card', ['title' => 'Чат', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')
        @endslot
            <section class="content-body pl-4 pr-4 pt-4 pb-5" >
                    <div class="col-sm-12 border rounded d-flex justify-content-between pr-0" style="height: 60vh; gap:2px;overflow: hidden;">
                        <div class="col-sm-5 pt-3 border-right d-flex flex-column" style="overflow: auto">
                            <form action="{{ route('admin2.chat.index-bookings') }}" id="search_form">
                                <div class="input-group d-flex align-items-center border pl-3 pr-3 mb-3" style="border-radius: 20px;">
                                    <input id="search-input" type="text" value="{{ $search ?? '' }}" name="search" class="form-control border-0 p-0" placeholder="Поиск по ID заказа">
                                    @if($search)
                                        <label for="search-input" class="mb-0 cursor-pointer" style="color:#C6C6C6;" onclick="$('#search-input').val('');$('#search_form').submit();">
                                            <i class="fa fa-times" aria-hidden="true"></i>
                                        </label>
                                    @else
                                        <label for="search--" class="mb-0 cursor-pointer" style="color:#C6C6C6;" onclick="$('#search_form').submit();">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                        </label>
                                    @endif
                                </div>
                            </form>
                            @foreach($chats as $chat)
                                <a href="{{ route('admin2.chat.index-bookings', ['chat_id' => $chat->id]) }}" class="">
                                    <div class="border rounded mb-3 mr-0 p-2 pb-3 d-flex flex-column {{ ($selectedChatId && $selectedChatId == $chat->id) ? 'border-primary': '' }}" style="gap:10px;box-shadow: 0px 0px 3px rgba(0, 44, 71, 0.3);">
                                        <div class="text-center d-flex justify-content-end position-absolute" style="right: 15px">
                                        </div>
                                        <div class="d-flex flex-row" style="gap:20px;">
                                            <img width="50px" height="50px" src="{{ asset('uploads/defaults/company.png') }}" alt="">
                                            <div class="">
                                                <p class="d-flex justify-content-between mb-1"><span class="font-weight-bold text-md">Заказ №{{$chat->id}}</span><span></span></p>
                                                <p class="mb-0"></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="col-sm-7 pr-0 d-flex flex-row">
                            <hr class="border m-0" style="height: 100%;">
                                <div class="col-sm-12 d-flex flex-column justify-content-between p-0">
                                    @if($selectedChatId)
                                    <div class="d-flex p-2 border-bottom">
                                        <div class="col-sm-8 d-flex" style="gap:10px;">
                                            <a href="{{ route('admin2.bookings.show', $selected_booking) }}" class="">
                                                <img src="{{ asset('uploads/defaults/company.png') }}" alt="" width="43px">
                                            </a>
                                            <div class="d-flex flex-column">
                                                <a href="{{ route('admin2.bookings.show', $selected_booking) }}" class="">
                                                <p class="font-weight-bold text-md mb-0">Заказ №{{$selected_booking->id}}</p>
{{--                                                <p class="mb-0 d-flex align-items-center" style="gap:5px;"><span>Не в сети</span><span>&#8226;</span><span>Был 3 часа назад</span></p>--}}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 d-flex align-items-center justify-content-end">
{{--                                            <img src="../dist/img/Options--.png" alt="">--}}
                                        </div>
                                    </div>
                                    <div class=" p-2 pt-4  " style="gap: 200px;overflow: auto" id="js-chat-fixed-block">
                                        <div class=" p-2 pt-4  " style="gap: 20px;">
                                            @forelse($chatSelected as $message)
                                                <div class="message p-3 mb-2 border rounded {{ $message->user_id == $selected_booking->user_id ?'float-left': 'float-right' }}" style="width: 80%;">
                                                    <div class="small text-muted mb-1">
                                                        {{ ($message->user->surname ?? '').' '.($message->user->name ?? '').' '.($message->user->middle_name ?? '') }} {{ $message->user_id == $selected_booking->user_id ?'(Клиент)':'' }}
                                                    </div>
                                                    <div class="mb-1">
                                                        {!! nl2br($message->message) !!}
                                                    </div>
                                                    <div class="small text-muted float-right">
                                                        {{ $message->created_at->format('H:i d.m.Y') }}
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="clearfix"></div>
                                            @empty
                                                <div class="text-center">
                                                    Нет сообщений
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                    @else
                                        <div class="p-2 text-center">
                                            {{ $chats->isEmpty()? "Нет переписок": "Выберите чат из списка" }}
                                        </div>
                                    @endif
                                        @if($selectedChatId)
                                        <form action="{{ route('admin2.chat.index-bookings', ['chat_id'=> $selectedChatId]) }}" method="post">
                                            @csrf

                                            <div class="px-3 input-group mb-3 align-items-center position-relative">
                                                <textarea required autofocus name="message" rows="3" class="form-control w-100" placeholder="Введите сообщение" style="background: #DEE5EF;border: none!important;">{{ old('message', '') }}</textarea>
                                                    <button class="btn btn-warning rounded-circle position-absolute" style="z-index: 900;right: 30px; bottom: 10px" type="submit" id="button-addon2">
                                                        <i class="fab fa-telegram-plane text-white"></i>
                                                    </button>
                                            </div>
                                        </form>
                                        @endif
                                </div>
                        </div>
                    </div>
            </section>

{{--        @slot('bottom')--}}
{{--            @include('ui.pagination', ['data' => $chats])--}}
{{--        @endslot--}}
    @endcomponent
@endsection

@push('styles')
    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush


@push('scripts')
    <script>
        $(function () {
            $('#js-chat-fixed-block').scrollTop($("#js-chat-fixed-block")[0].scrollHeight);
        });
    </script>
@endpush
