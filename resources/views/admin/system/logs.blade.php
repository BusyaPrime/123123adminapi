@extends('admin.layout')
@section('center_content')
    <div class="p-4">
    @component('component.card', ['title' => 'Логи системы', 'bodyClass' => 'card-body-no-padding'])
    @slot('buttons')
        <div class="col-sm-6 d-flex justify-content-end">
            <a href="{{ route('admin.system.clearLogs') }}" class="btn btn-success">Очистить логи</a>
        </div>
    @endslot

        @if ($logs->count() > 0)
            <table style="border-spacing: 0;" class="table table-bordered table-hover" cellspacing="0" cellpadding="0">
                <thead>
                    <td class="w-25 p-4">Дата</td>
                    <td>Описание</td>
                    {{-- <td>Стек</td> --}}
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td class="w-25">
                                <p class="p-4 text-{{ $log['level_class']??'' }}">{{ $log['date']??'' }}</p>
                            </td>
                            <td class="text-justify" style="word-break:break-all">
                                <p class="p-4 text-left text-{{ $log['level_class']??'' }}">{{ $log['text']??'' }}</p>
                            </td>
                            {{-- <td>
                                <p class="{{ $log['level_class']??'' }}">{{ $log['stack']??'' }}</p>
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @slot('bottom')
            @include('ui.pagination', ['data' => $logs])
        @endslot
    @endcomponent
    </div>
@endsection