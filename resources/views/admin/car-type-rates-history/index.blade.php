@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => trans('История изменений тарифов'), 'bodyClass' => 'card-body-no-padding'])
        @if($histories->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">Тип машины</th>
                            <th  class="table__th">Направление</th>
                            <th  class="table__th">Сезон</th>
                            <th class="table__th">Изменения</th>
                            <th class="table__th">Дата изменения</th>
                            <th class="table__th"></th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($histories as $item)
                        <tr class="table__tr mt-2 mb-2" data-toggle="modal" data-target="#{{ 'item_'.$item->id }}">
                            <td class="table__td" style="vertical-align:center;">
                                <img src="{{ $item->car_type->imageUrl() }}" alt="{{ $item->car_type->title }}" width="70px">
                                <span style=" display:inline-block; margin:0 15px;">{{ $item->car_type->translations[1]->title }}</span>
                            </td>
                            <td class="table__td">
                                <span>{{ $item->region_from->title }}</span>
                                <i class="fas fa-arrow-right" style="margin: 0 10px; font-size:11px;"></i>
                                <span>{{ $item->region_to->title }}</span>
                            </td>
                            <td class="table__td">
                                <span>{{ $item->season ? $item->season->title : 'Не назначен' }}</span>
                            </td>
                            
                            <td class="table__td">
                                @php
                                    $changed_fields = json_decode($item->changed_fields, true);
                                    $previous_values = json_decode($item->previous_values, true);
                                @endphp
                                @foreach($changed_fields as $field)
                                    @php
                                        $_index = $loop->index;
                                    @endphp
                                    @if($loop->index > 0)
                                        {{ '...' }}
                                        @break
                                    @endif
                                    @foreach(array_keys($field) as $key)
                                        <div style="font-size:11px;color:var(--green);">@lang('admin.changed_fields.'.$key)</div>
                                    @endforeach
                                    @if($loop->index != (count($changed_fields) - 1)) <hr style="padding:1px; margin:1px;" /> @endif
                                @endforeach
                            </td>
                            
                            <td class="table__td">
                                <span>{{ $item->created_at->format("d/m/Y H:i") }}</span>
                            </td>


                            <td class="table__td">
                                <span>
                                    <a href="#" class="{{ 'item_'.$item->id }}" style="color:var(--orange); padding:5px 25px; " data-toggle="modal" data-target="#{{ 'item_'.$item->id }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <div class="modal fade" id="{{ 'item_'.$item->id }}" tabindex="-1" role="dialog" aria-hidden="true" style="padding:20px 40px;">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content w-100" style="width:400px;">
                                            <div class="modal-header" style="padding:20px 35px;">
                                                <h5 class="modal-title">Дата изменения: {{ $item->created_at->format('d/m/Y H:i') }}</h5>
                                            </div>
                                            <div class="modal-body" style="width:500px;">
                                                @foreach($changed_fields as $_i => $chField)
                                                    @php
                                                        $_key = array_keys($chField)[0];
                                                    @endphp
                                                    <div class="row" style="padding-bottom:15px;">
                                                        <div class="col">
                                                            <h5>@lang('admin.changed_fields.'.$_key)</h5>
                                                            @if($_key != 'prices_distance' && $_key != 'prices')
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <div>
                                                                            Прошлое значение: {{ $previous_values[$_i][$_key]}}
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-6">
                                                                        Новое значение: {{ $item->car_type_rate->$_key }}
                                                                    </div>
                                                                </div>
                                                            @elseif($_key == 'prices_distance')
                                                                @php
                                                                    $pricesDistance = json_decode($chField[$_key], true);
                                                                    $pricesDistance_prev = json_decode($previous_values[$_i][$_key], true);
                                                                @endphp
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <div>
                                                                            Прошлое значение:
                                                                            @foreach($pricesDistance_prev as $price)
                                                                                <div>
                                                                                    <span>{{ $price['distance']??'' }} км: </span>
                                                                                    <span>{{ $price['sum'] }}</span>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-6">
                                                                        Новое значение:
                                                                            @foreach(json_decode($item->car_type_rate->$_key, true) as $new_price)
                                                                                <div>
                                                                                    <span>{{ $new_price['distance']??'' }} км: </span>
                                                                                    <span>{{ $new_price['sum'] }}</span>
                                                                                </div>
                                                                            @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </span>
                                <!-- <div class="dropdown">
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin.car-types.edit', $item) }}" class="dropdown-item">
                                            @lang('admin.show')
                                        </a>
                                    </div>
                                </div> -->
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        @slot('bottom')
            @include('ui.pagination', ['data' => $histories])
        @endslot
    @endcomponent
@endsection
