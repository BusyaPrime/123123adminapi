@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Статус сокетов', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')
            <a href="{{ route('admin.sockets.restart', ['redis_pid' => $redis_status > 0 ? $redis_status: '', 'echo_server_pid' => $echo_server_status > 0 ? $echo_server_status: '']) }}" class="btn btn-sm btn-warning ml-2">
                <span class="d-none d-sm-inline-block">Перезапустить</span> <i class="icmn-spinner11"><!-- --></i>
            </a>
        @endslot

        <div class="row">
            <div class="col-md-6">
                <span class="badge {{ $redis_status > 0 ? 'badge-success': 'badge-danger' }}">Redis: {{ $redis_status > 0 ? 'Вкл': 'Выкл' }}.</span>
            </div>
            <div class="col-md-6">
                <span class="badge {{ $echo_server_status > 0 ? 'badge-success': 'badge-danger' }}">Laravel Echo: {{ $echo_server_status > 0 ? 'Вкл': 'Выкл' }}.</span>
            </div>
        </div>

    @endcomponent
@endsection
