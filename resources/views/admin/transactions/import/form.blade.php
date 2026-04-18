@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.transactions.import-show') }}" method="post" enctype="multipart/form-data">
        @csrf
        @component('component.card', ['title' => 'Шаг 1: Банковская выписка'])

            <div class="form-group row {!! $errors->first('file', 'has-danger')!!}">
                <label class="col-md-3 text-md-right col-form-label-sm" for="file">Выбрать файл</label>
                <div class="col-md-4">
                    <input type="file" name="file" class="form-control input-sm " id="file"  >
                    {!! $errors->first('file', '<small class="form-control-feedback">:message</small>') !!}
                </div>
            </div>
            @slot('bottom')

                <div class="px-5 py-3">
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                    <button class="btn btn-sm btn-primary float-right">Загрузить</button>
                    <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
    @if(isset($transactions))
    <form action="{{ route('admin.transactions.import-excel') }}" method="post" enctype="multipart/form-data">
        @csrf
        @component('component.card', ['title' => 'Шаг 2: Предпросмотр и импорт'])
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                    <tr>
                        <th class="table__th"></th>
                        <th class="table__th">ИНН компании</th>
                        <th class="table__th">Сумма</th>
                        <th class="table__th">Основание</th>
                    </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($transactions as $i => $transaction)
                        <tr class="table__tr mt-2 mb-2 "  >
                        <td class="table__td">
                            <input type="hidden" name="transactions[{{$i}}][include]" value="0">

                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="transactions_include_{{$i}}" value="1" name="transactions[{{$i}}][include]" checked>
                                <label class="text-secondary" for="transactions_include_{{$i}}">

                                </label>
                            </div>
                        </td>
                        <td class="table__td">
                                <input type="text" class="form-control" name="transactions[{{$i}}][inn]"  placeholder="Введите ИНН компании" value="{{$transaction['inn'] ?? ''}}">
                        </td>
                        <td class="table__td">
                                <input type="number" class="form-control" min="0" step="1" name="transactions[{{$i}}][amount]"  placeholder="Введите сумму" value="{{$transaction['sum'] ?? ''}}">
                        </td>
                        <td class="table__td">
                                <textarea type="text" class="form-control" name="transactions[{{$i}}][description]"  placeholder="На каком основании транзакция" >{{$transaction['description'] ?? ''}}</textarea>
                        </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @slot('bottom')

                <div class="px-5 py-3">
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                    <button class="btn btn-sm btn-success float-right">Импортировать</button>
                    <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
    @endif

@endsection
