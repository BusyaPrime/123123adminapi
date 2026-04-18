@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Задолженность (Водители)', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')
            <div class="col-6 text-right">
                <a href="{{ route('admin.transactions.debts.users.export') }}" class="btn btn-default mb-2 ml-2">
                    <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
                </a>
            </div>
        @endslot
        @if($cars->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                    <tr>
                        <th class="table__th">@lang('validation.attributes.id')</th>
                        <th class="table__th">@lang('validation.attributes.name')</th>
                        {{--                            <th class="table__th"></th>--}}
                                                    <th class="table__th">Тип транспорт</th>
                        <th class="table__th">Рейтинг</th>
                        <th class="table__th">Дата регистрации</th>
                        <th class="table__th">Заказов</th>
                        <th class="table__th">Задолженность</th>
                        <th class="table__th">@lang('validation.attributes.active')</th>
                        <th class="table__th"></th>
                    </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($cars as $car)
                        <tr class="table__tr mt-2 mb-2 " >
                            <td class="table__td">{{ $car->user->id ?? $car->id }}</td>
                            <td class="table__td">{{ ($car->user->profile->surname ?? '').' '.($car->user->profile->name ?? '').' '.($car->user->profile->middle_name ?? '') }}</td>
                            <td>
                                @if($car->carType)
                                    {{ $car->carType->title }}
                                @else
                                    Не узакан
                                @endif
                            </td>
                            <td class="table__td">
                                @if($car->user && $car->user->profile)
                                    {{ $car->user->profile->rating ?? 'Нет оценки' }}
                                @else
                                    Нет оценки
                                @endif
                            </td>
                            <td class="table__td">
                                {{ $car->created_at->format('d.m.Y') }}
                            </td>
                            <td class="table__td">
                                @if($car->user)
                                    {{ \App\Domain\TruckBookings\Models\TruckBooking::where('driver_id', $car->user->id)->count() ?? 0}}
                                @else
                                    0
                                @endif
                            </td>
                            <td class="table__td">
                                <span class="text-danger">{{ $car->user->profile->balance ?? 0 }}</span>
                            </td>
                            <td class="table__td">
                                {!! $car->active? '<span class="badge badge-success">'.trans('admin.active').'</span>': '<span class="badge badge-danger">'.trans('admin.not_active').'</span>' !!}
                            </td>
                            <td class="table__td">
                                <a href="{{ route('admin.cars.show', $car) }}" class="dropdown-item">
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
            @include('ui.pagination', ['data' => $cars])
        @endslot
    @endcomponent
@endsection
