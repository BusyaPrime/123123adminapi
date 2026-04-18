@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Процентная ставка', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')


<div class="row col-sm-6 justify-content-end">
    <a href="{{ route('admin.commission-rates.create') }}" class="btn btn-info ml-2">
        <span class="d-none d-sm-inline-block">@lang('admin.create')</span> <i class="icmn-plus"><!-- --></i>
    </a>
</div>

        @endslot

        @if($rates->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">@lang('validation.attributes.id')</th>
                            <th class="table__th">@lang('validation.attributes.title')</th>
                            <th class="table__th">Комиссия</th>
                            <th class="table__th"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($rates as $item)
                        <tr class="table__tr mt-2 mb-2 ">
                            <td class="table__td">{{ $item->id }}</td>
                            <td class="table__td">{{ $item->title }}</td>
                            <td class="table__td">{{ $item->commission ?? '0' }}%</td>
                            <td class="table__td">
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin.commission-rates.edit', $item) }}" class="dropdown-item">
                                            @lang('admin.edit')
                                        </a>
                                        <div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.destroy_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.commission-rates.destroy', $item) }}" id="delete_form" class="d-inline-block" method="post">
                                                @csrf
                                                @method('delete')
                                                <span class="d-block text-danger"
                                                      >
                                                    @lang('admin.delete')
                                                </span>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @slot('bottom')
            @include('ui.pagination', ['data' => $rates])
        @endslot
    @endcomponent
@endsection
