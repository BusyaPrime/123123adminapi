@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Задолженность (Логистические Компании)', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')
            <div class="col-6 text-right">
                <a href="{{ route('admin.transactions.debts.companies.export') }}" class="btn btn-default mb-2 ml-2">
                    <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
                </a>
            </div>
        @endslot

        @if($companies->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">@lang('admin.id')</th>
                            <th class="table__th">@lang('validation.attributes.title')</th>
                            <th class="table__th">@lang('validation.attributes.phone')</th>
                            {{--<th>@lang('common.phones')</th>--}}
                            {{--<th>@lang('common.emails')</th>--}}
                            {{--<th>@lang('common.console_number')</th>--}}
                            <th class="table__th">Дата регистрации</th>
                            <th class="table__th">Задолженность</th>
                            <th class="table__th">@lang('validation.attributes.active')</th>
                            <th class="table__th"></th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($companies as $company)
                        <tr class="table__tr mt-2 mb-2 "  >
                            <td class="table__td">{{ $company->id }}</td>
                            <td class="table__td">{{ $company->title }}</td>
                            {{--<td>{!! nl2br($company->address) !!}</td>--}}
                            {{--<td>--}}
                                {{--@foreach($company->getParsedPhones() as $phone)--}}
                                    {{--{{ $phone }}--}}
                                    {{--@if(!$loop->last) <br> @endif--}}
                                {{--@endforeach--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--@foreach($company->getParsedEmails() as $email)--}}
                                    {{--{{ $email }}--}}
                                    {{--@if(!$loop->last) <br> @endif--}}
                                {{--@endforeach--}}
                            {{--</td>--}}
                            {{--<td>{{ $company->console_number }}</td>--}}
                            <td class="table__td">
                                @if($company->user && $company->user)
                                    {{ $company->user->username }}
                                @else
                                    Не указан
                                @endif
                            </td>
                            <td class="table__td">
                                {{ $company->created_at->format('d.m.Y') }}
                            </td>
                            <td class="table__td">
                                <span class="text-danger">{{ $company->balance }}</span>
                            </td>
                            <td class="table__td">
                                {!! $company->active? '<span class="badge badge-success">'.trans('admin.active').'</span>': '<span class="badge badge-danger">'.trans('admin.not_active').'</span>' !!}
                            </td>
                            <td class="table__td">
                                <a href="{{ route('admin.companies.show', $company) }}" class="dropdown-item">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @slot('bottom')
            @include('ui.pagination', ['data' => $companies])
        @endslot
    @endcomponent
@endsection
