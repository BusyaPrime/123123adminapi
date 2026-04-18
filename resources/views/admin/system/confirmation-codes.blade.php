@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Коды подтверждения', 'bodyClass' => 'card-body-no-padding'])
        @if ($codes->count() > 0)
            <table style="border-spacing: 0;" class="table table-bordered table-hover" cellspacing="0" cellpadding="0">
                <thead>
                    <td class="p-2">Пользователь</td>
                    <td class="p-2">Телефон</td>
                    <td>Код</td>
                    <td>Дата</td>
                    {{-- <td>Стек</td> --}}
                </thead>
                <tbody>
                    @foreach ($codes as $code)
                        <tr>
                            <td class="p-2">
                                <span>
                                    {{$code->user->profile->surname??'Без имени'}} {{$code->user->profile->name??''}} (ID{{$code->user->id??''}})
                                </span>
                            </td>
                            <td class="p-2">
                                <span>
                                    {{'+' . $code->user->username??''}}
                                </span>
                            </td>
                            <td class="p-2">
                                <span>
                                    {{$code->code ??''}}
                                </span>
                            </td>
                            <td class="p-2 w-25">
                                <span>
                                    {{$code->created_at->format('d M Y \a\t H:i:s') ??''}}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @slot('bottom')
            @include('ui.pagination', ['data' => $codes])
        @endslot
    @endcomponent
@endsection
