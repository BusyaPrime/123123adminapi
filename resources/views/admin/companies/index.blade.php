@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Компании', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')

<div class="row col-sm-6 justify-content-end">

    <a href="{{ route('admin.companies.export', $filters) }}" class="btn btn-default ml-2">
        <span class="d-none d-sm-inline-block">Экспорт</span> <i class="icmn-plus"><!-- --></i>
    </a>
    @if(($filters['title'] != '' || $filters['created_at'] != '' || $filters['active'] != ''))
        <button type="button" class="btn btn-default  ml-2" id="open_filter" style="display: none;">
            <span class=" ml-2">Фильтр</span>
            <i class="fas fa-chevron-down text-black pl-2 pr-2" style="color: #2b2b2b!important;"></i>
        </button>
        <button type="button" class="btn btn-default  ml-2" id="close_filter" style="">
            <span class="text-secondary ml-2">Фильтр</span>
            <i class="fas fa-chevron-up text-black pl-2 pr-2" ></i>
        </button>
    @else
        <button type="button" class="btn btn-default  ml-2" id="open_filter">
            <span class=" ml-2">Фильтр</span>
            <i class="fas fa-chevron-down text-black pl-2 pr-2" style="color: #2b2b2b!important;"></i>
        </button>
        <button type="button" class="btn btn-default  ml-2" id="close_filter" style="display: none;">
            <span class="text-secondary ml-2">Фильтр</span>
            <i class="fas fa-chevron-up text-black pl-2 pr-2" ></i>
        </button>
    @endif
    <a href="{{ route('admin.companies.create') }}" class="btn btn-info ml-2 px-3">
        <span class="d-none d-sm-inline-block">Добавить компанию</span> <i class="icmn-plus"><!-- --></i>
    </a>
</div>
        @endslot
        @slot('filters')
            <section class="content" id="filter_block" style="{{ ($filters['title'] != '' || $filters['created_at'] != '' || $filters['active'] != '' || $filters['role'] != '') ?'':'display: none' }};">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <form method="get" action="{{ route('admin.companies.index') }}" id="filter_block_form">
                                <div class="border rounded p-3">
                                    <div class="form-group">
                                        <div class="row justify-content-between">

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <input type="text" name="title" value="{{ $filters['title'] }}" class="form-control" placeholder="Поиск по наименованию">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="role">
                                                        <option value="">Тип компании</option>
                                                        <option value="{!! \App\Domain\Companies\Models\Company::ROLE_COMPANY !!}" {{ $filters['role'] == \App\Domain\Companies\Models\Company::ROLE_COMPANY ? 'selected':'' }}>@lang('admin.company_types.'.\App\Domain\Companies\Models\Company::ROLE_COMPANY)</option>
                                                        <option value="{!! \App\Domain\Companies\Models\Company::ROLE_LOGISTICS !!}" {{ $filters['role'] == \App\Domain\Companies\Models\Company::ROLE_LOGISTICS ? 'selected':'' }}>@lang('admin.company_types.'.\App\Domain\Companies\Models\Company::ROLE_LOGISTICS)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="active">
                                                        <option value="">Статус компании</option>
                                                        <option value="1" {{ $filters['active'] == 1? 'selected':'' }}>@lang('admin.active')</option>
                                                        <option value="0" {{ $filters['active'] == 0 && $filters['active'] != ''? 'selected':'' }}>@lang('admin.not_active')</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <select class="form-control" name="sort">
                                                        <option value="-id" {{ $filters['sort'] == '-id'? 'selected':'' }}>Сначала новые</option>
                                                        <option value="id" {{ $filters['sort'] == 'id'? 'selected':'' }}>Сначала старые</option>
                                                        <option value="title" {{ $filters['sort'] == 'title'? 'selected':'' }}>По наименованию (A-Z)</option>
                                                        <option value="-title" {{ $filters['sort'] == '-title'? 'selected':'' }}>По наименованию (Z-A)</option>
                                                        <option value="cars" {{ $filters['sort'] == 'cars'? 'selected':'' }}>Меньше всего авто</option>
                                                        <option value="-cars" {{ $filters['sort'] == '-cars'? 'selected':'' }}>Больше всего авто</option>
                                                        <option value="-active" {{ $filters['sort'] == '-active'? 'selected':'' }}>Сначала активные</option>
                                                        <option value="active" {{ $filters['sort'] == 'active'? 'selected':'' }}>Сначала заблокированные</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row align-items-center justify-content-end">
                                            <div class="col-sm-6 d-flex align-items-center justify-content-end">
                                                <a href="{{ route('admin.companies.index') }}" class="btn btn-danger mr-2" style="{{ ($filters['title'] != '' || $filters['created_at'] != '' || $filters['active'] != '') ?'':'display: none' }};">Сбросить фильтры</a>
                                                <a onclick="$('#filter_block_form').submit();" class="btn btn-info">Применить</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        @endslot

        @if($companies->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">@lang('admin.id')</th>
                            <th class="table__th">@lang('validation.attributes.title')</th>
                            <th class="table__th">Кол-во поль.</th>
                            <th class="table__th">@lang('validation.attributes.phone')</th>
                            {{--<th>@lang('common.phones')</th>--}}
                            {{--<th>@lang('common.emails')</th>--}}
                            {{--<th>@lang('common.console_number')</th>--}}
                            <th class="table__th">Дата регистрации</th>
                            <th class="table__th">@lang('validation.attributes.active')</th>
                            <th class="table__th">Роль</th>
                            <th class="table__th"></th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($companies as $company)
                        <tr class="table__tr mt-2 mb-2 "  >
                            <td class="table__td">{{ $company->id }}
							
							</td>
                            <td class="table__td">{{ $company->title }}</td>
                            <td class="table__td">
                                {{ $company->users_count ?? 0 }}
                            </td>
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
                                @if($company->user)
                                    {{ $company->user->profile->phone_number }}
                                @else
                                    Не указан
                                @endif
                            </td>
                            <td class="table__td">
                                {{ $company->created_at->format('d.m.Y') }}
                            </td>
                            <td class="table__td">
                                {!! $company->active? '<span class="badge badge-success">'.trans('admin.active').'</span>': '<span class="badge badge-danger">'.trans('admin.not_active').'</span>' !!}
                            </td>
                            <td class="table__td">
                                {!!trans("admin.company_types.".$company->role)!!}
                            </td>
							<td class="table__td">
                                @if($company->user && $company->user->is_external == 1 && $company->user->is_verified == 0)
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M21.76 15.92L15.36 4.4C14.5 2.85 13.31 2 12 2C10.69 2 9.50001 2.85 8.64001 4.4L2.24001 15.92C1.43001 17.39 1.34001 18.8 1.99001 19.91C2.64001 21.02 3.92001 21.63 5.60001 21.63H18.4C20.08 21.63 21.36 21.02 22.01 19.91C22.66 18.8 22.57 17.38 21.76 15.92ZM11.25 9C11.25 8.59 11.59 8.25 12 8.25C12.41 8.25 12.75 8.59 12.75 9V14C12.75 14.41 12.41 14.75 12 14.75C11.59 14.75 11.25 14.41 11.25 14V9ZM12.71 17.71C12.66 17.75 12.61 17.79 12.56 17.83C12.5 17.87 12.44 17.9 12.38 17.92C12.32 17.95 12.26 17.97 12.19 17.98C12.13 17.99 12.06 18 12 18C11.94 18 11.87 17.99 11.8 17.98C11.74 17.97 11.68 17.95 11.62 17.92C11.56 17.9 11.5 17.87 11.44 17.83C11.39 17.79 11.34 17.75 11.29 17.71C11.11 17.52 11 17.26 11 17C11 16.74 11.11 16.48 11.29 16.29C11.34 16.25 11.39 16.21 11.44 16.17C11.5 16.13 11.56 16.1 11.62 16.08C11.68 16.05 11.74 16.03 11.8 16.02C11.93 15.99 12.07 15.99 12.19 16.02C12.26 16.03 12.32 16.05 12.38 16.08C12.44 16.1 12.5 16.13 12.56 16.17C12.61 16.21 12.66 16.25 12.71 16.29C12.89 16.48 13 16.74 13 17C13 17.26 12.89 17.52 12.71 17.71Z" fill="#ffc107"/>
</svg>

							@endif
								
                            </td>
                            <td class="table__td">
                                <div class="dropdown"  >
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin.companies.show', $company) }}" class="dropdown-item">
                                            Просмотр
                                        </a>
                                        <a href="{{ route('admin.companies.edit', $company) }}" class="dropdown-item">
                                            @lang('admin.edit')
                                        </a>
                                        <div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.destroy_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.companies.destroy', $company) }}" id="delete_form" class="d-inline-block" method="post">
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
            @include('ui.pagination', ['data' => $companies])
        @endslot
    @endcomponent
@endsection
