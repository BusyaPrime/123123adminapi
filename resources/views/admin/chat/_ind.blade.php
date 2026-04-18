@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Чат', 'bodyClass' => 'card-body-no-padding'])
        <div class="row">
            <div class="col-lg-3 p-0">
                <div class="text-white p-3 border border-light bg-dark">
                    Чаты
                </div>
                @foreach($chats as $chat)
                    <div class="row ">
                        <div class="col">
                            <a href="{{ route('admin.chat.index', ['chat_id' => $chat->id]) }}" class="">
                                <div class="p-3 text-white  border border-light bg-{{ ($selectedChatId && $selectedChatId == $chat->id) ? 'dark': 'secondary' }}">
                                    <div class="row ">
                                        <div class="col-3">
                                            <div class="">
                                                <img src="{{ $chat->profile->imageUrl() }}" alt="{{ $chat->profile->name }}" class="img-fluid rounded-circle img-thumbnail">
                                            </div>
                                        </div>
                                        <div class="col-9 d-flex align-items-center ">
                                            <div class="">
                                                {{ $chat->profile->name }}
                                                <br>
                                                <small class="small text-muted">
                                                    ID: {{ $chat->id }}
                                                </small>
                                                @if(($count = $chat->adminMessages()->whereNotNull('user_id')->where('read', 0)->count()) > 0)
                                                    <span class="badge badge-success">
                                                    {{ $count > 9 ? '9+': $count }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-lg-9 p-0  border-dark border" >
                <div class="text-white p-3 border border-light bg-dark">
                    Переписка
                </div>
                <div class="p-3" style="height: 50vh; overflow: auto" id="js-chat-fixed-block">
                    @if($selectedChatId)
                        @forelse($chatSelected as $message)
                            <div class="message p-3 mb-3 text-white {{ $message->user_id == $message->chat_id ? ' float-left bg-info': ' float-right bg-success' }}" style="width: 80%;">
                                <div class="mb-1">
                                    {!! nl2br($message->message) !!}
                                </div>
                                <div class="small text-white float-right">
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
                    @else
                        <div class="text-center">
                            {{ $chats->isEmpty()? "Нет переписок": "Выберите чат или начните новую переписку" }}
                        </div>
                    @endif
                </div>

                <form action="{{ route('admin.chat.index', ['chat_id'=> $selectedChatId]) }}" method="post">
                    @csrf
                    @if(!$selectedChatId)
                        <h5 class="text-center">
                            Начать новую переписку
                        </h5>
                        <div class="form-group  px-3">
                            <select name="chat_id" id="chat_id" class="select2 form-control" required>
                                <option value="">Выберите пользователя</option>
                                @foreach(\App\Domain\Users\Models\User::where('role', '!=', 'admin')->with('profile')->get() as $user)
                                    <option value="{{ $user->id }}" {{ old('chat_id', 0) == $user->id ? 'selected': '' }}>{{ $user->car ? 'Водитель': 'Клиент' }} - {{ $user->profile->name }} (ID: {{ $user->id }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="px-3 input-group mb-3 align-items-center">
                        <textarea required autofocus name="message" rows="1" class="form-control" placeholder="Введите текст сообщения">{{ old('message', '') }}</textarea>
                        <div class="input-group-append">
                            <button class="btn btn-success" type="submit" id="button-addon2">
                                <i class="icmn-mail4"></i> Отправить
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcomponent
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#js-chat-fixed-block').scrollTop($("#js-chat-fixed-block")[0].scrollHeight);
        });
    </script>
@endpush

