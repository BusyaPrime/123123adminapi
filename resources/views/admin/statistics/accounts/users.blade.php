@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Аналитика по аккаунтам', 'bodyClass' => 'card-body-no-padding'])

        @slot('buttons')
            <div class="row col-sm-6 justify-content-end">
                <form id="statistics_form" class="col-12 row" action="{{ route('admin.statistics.accounts.users') }}">

                    <div class="col-9 text-right">
                        <a href="{{ route('admin.statistics.accounts.users.export', ['preset' => $preset]) }}" class="btn btn-default mr-3">
                            <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
                        </a>
                        <a href="{{ route('admin.statistics.accounts.drivers') }} "  class="btn btn-default mr-3">Водители</a>
                        <a  class="btn btn-primary active   mr-3">Пользователи</a>
                    </div>
                    <div class="col-3">

                        <div class="form-group d-inline">
                            <select class="form-control js-presets" name="preset">
                                <option value="all" {{ ($preset == 'all') ? 'selected':'' }}>Все</option>
                                <option value="week" {{ ($preset == 'week') ? 'selected':'' }}>Неделя</option>
                                <option value="month" {{ ($preset == 'month') ? 'selected':'' }}>Месяц</option>
                                <option value="months" {{ ($preset == 'months') ? 'selected':'' }}>3 Месяца</option>
                            </select>
                        </div>

                    </div>

                </form>

                {{--    <button type="button" class="btn btn-default  ml-2">Экспорт</button>--}}
            </div>

        @endslot


        @if($users->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                    <tr>
                        <th class="table__th">@lang('validation.attributes.id')</th>
                        <th class="table__th">@lang('validation.attributes.name')</th>
{{--                        <th class="table__th">Тип транспорт</th>--}}
                        <th class="table__th">Рейтинг</th>
                        <th class="table__th">Дата регистрации</th>
                        <th class="table__th">Заказов</th>
                        <th class="table__th">Последний раз был в сети</th>
                    </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($users as $user)
                        <tr class="table__tr mt-2 mb-2 " >
                            <td class="table__td">{{ $user->id ?? '' }}</td>
                            <td class="table__td">{{ ($user->profile->surname ?? '').' '.($user->profile->name ?? '').' '.($user->profile->middle_name ?? '') }}</td>
{{--                            <td>--}}
{{--                                @if($user->car->carType)--}}
{{--                                    {{ $user->car->carType->title }}--}}
{{--                                @else--}}
{{--                                    Не узакан--}}
{{--                                @endif--}}
{{--                            </td>--}}
                            <td class="table__td">
                                @if($user->profile)
                                    {{ $user->profile->rating ?? 'Нет оценки' }}
                                @else
                                    Нет оценки
                                @endif
                            </td>
                            <td class="table__td">
                                {{ $user->created_at->format('d.m.Y') }}
                            </td>
                            <td class="table__td">
                                {{ \App\Domain\TruckBookings\Models\TruckBooking::where('user_id', $user->id)->count() ?? 0}}
                            </td>
                            <td class="table__td">
                                @if($user->last_seen_at)
                                    <span class=" {{ $user->last_seen_at->gt(date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00', now()->subWeek()->timestamp)))) ? 'text-success': ($user->last_seen_at->gt(date('Y-m-d 00:00:00', now()->subMonth()->timestamp)) ? 'text-warning':'text-danger') }}">
                                        {{ $user->last_seen_at->format('d.m.Y') }}
                                    </span>
                                @else
                                    <span class="text-danger">
                                        Не был в сети
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif


        @slot('bottom')

            <div class="px-5 py-3 text-center">
                @include('ui.pagination', ['data' => $users])
            </div>

            <div class="px-5 py-3 text-center">
                <a href="{{ route('admin.statistics.index') }}" class="btn  btn-primary ">Вернуться в общую статистику</a>
            </div>
        @endslot

        {{--        @slot('bottom')--}}
        {{--            @include('ui.pagination', ['data' => $users])--}}
        {{--        @endslot--}}
    @endcomponent

@endsection

@push('scripts')
    <script>
        $(function () {
            $('.js-presets').on('change', function () {
                $('#statistics_form').submit();
            });
        });
    </script>
@endpush
