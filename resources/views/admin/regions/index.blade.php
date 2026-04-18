@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => trans('admin.nav.regions'), 'bodyClass' => 'card-body-no-padding'])
        @if($regions->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">@lang('validation.attributes.id')</th>
                            <th class="table__th">@lang('validation.attributes.title')</th>
{{--                            <th class="table__th"></th>--}}
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($regions as $region)
                        <tr class="table__tr mt-2 mb-2 ">
                            <td class="table__td">{{ $region->id }}</td>
                            <td class="table__td">{{ $region->title }}</td>
{{--                            <td class="table__td">--}}
{{--                                <a href="{{ route('admin.regions.edit', $region) }}" class="text-white btn btn-sm btn-warning" data-toggle="tooltip" data-placement="top" title="@lang('admin.edit')">--}}
{{--                                    <i class="fas fa-edit"></i>--}}
{{--                                </a>--}}
{{--                            </td>--}}
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @slot('bottom')
            @include('ui.pagination', ['data' => $regions])
        @endslot
    @endcomponent
@endsection
