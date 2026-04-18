@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => trans('admin.statistics'), 'bodyClass' => 'card-body-no-padding'])


        @if($users->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">@lang('validation.attributes.id')</th>
                            <th class="table__th">@lang('validation.attributes.name')</th>
                            <th class="table__th">Тип аккаунта</th>
                            <th class="table__th">Дата регистрации</th>
                            <th class="table__th">Количество заказов</th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($users as $user)
                        @php
                            $userCompany = \App\Domain\Companies\Models\Company::where('user_id', $user->id)->first();
                            $companyName = null;
                            if($userCompany){
                                $companyName = $userCompany->title;
                            }
                        @endphp
                        <tr class="table__tr mt-2 mb-2" onclick="window.location.href = '{{route('admin.users.show', $user)}}'">
                            <td class="table__td">{{ $user->id }}</td>
                            <td class="table__td">{{ ($user->profile->surname ?? '').' '.($user->profile->name ?? '').' '.($user->profile->middle_name ?? '') }}</td>
                            <td class="table__td">{{ $user->profile->company && $user->profile->is_external != 1 ? $user->profile->company->title : $companyName ?? 'Физическое лицо'}}</td>
                            <td class="table__td">{{ $user->created_at->format('d.m.Y') }}</td>
                            <td class="table__td">{{ $user->bookings_count }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        
        @slot('bottom')
            @include('ui.pagination', ['data' => $users])
        @endslot

    @endcomponent
@endsection
