<div class="row">
    <div class="col-md-12">
        
        <div class="form-group row {!! $errors->first('restricted_weight', 'has-danger')!!}">
            <label class="col-md-3 text-md-right col-form-label-sm" for="restricted_weight">Ограниченная грузоподъемность </label>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="number" step="any" min="0" name="restricted_weight" class="form-control input-sm" id="restricted_weight" value="{{ old('restricted_weight', $carType->limited_weight ?? 0) }}"  >
                    <span class="input-group-text">кг</span>
                </div>
                {!! $errors->first('restricted_weight', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>

        
        <table class="w-50" style="border-spacing:0!important;">
            @for($i = 0; $i < ($regions->count() + 1); $i++)
            @php
                $region_from = $i == 0 ? null : $regions[$i-1];
            @endphp
                <tr class="table__tr">
                @for($j = 0; $j < ($regions->count() + 1); $j++)
                    @php
                        $region_to = $j == 0 ? null : $regions[$j-1];
                    @endphp


                    @if($i == 0 && $j != 0)
                        <td class="text-center"><span class="text-center" style="padding:50px 0!important; display:inline-block; font-size:11px;transform: rotate(-90deg);">{{ $region_to->title}}</span></td>
                    @else
                        @if($j == 0)
                            @if($i == 0)
                                <td class="table__td" style="padding:5px 0!important;"></td>
                            @else
                                <td class="table__td" style="padding:5px 10px!important;"><span class="small">{{ $region_from->title }}</span></td>
                            @endif
                        @else
                            <td class="table__td hoverable text-center" style="padding:5px 0!important;transition:.1s background;" data-row="{{ $i }}" data-col="{{ $j }}">
                                @php
                                    $directions = $carType->limited_directions ? json_decode($carType->limited_directions, true) : [];
                                @endphp
                                <input type="checkbox" class="dir_checkbox" id="d_{{ $region_from->id.'_'.$region_to->id }}" name="directions[direction][]" value="{{ $region_from->id.'_'.$region_to->id}}" {{ in_array($region_from->id.'_'.$region_to->id, $directions) ? 'checked' : '' }}>
                            </td>
                        @endif
                    @endif
                @endfor
                </tr>
            @endfor
        </table>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        $(function(){
            $checkBox = $('.dir_checkbox');

            $checkBox.on('change', function(e){
                let id = $(e.currentTarget).attr('id');
                let reverseId = `${id.slice(0, 2)}${id.slice(2, id.length).split('_')[1]}_${id.slice(2, id.length).split('_')[0]}`;
                const $reverseObj = $(`#${reverseId}`);
                $reverseObj.attr('checked', !$reverseObj.attr('checked'))
            });


            $td = $('.table__td.hoverable');
            $tr = $('.table__tr');
            
            $td.hover((e) => {
                let $el = $(e.currentTarget);
                let row = $el.attr('data-row');
                let col = $el.attr('data-col') - 1;

                $tr.map((index, _el) => {
                    const $_el = $(_el);
                    if(e.type == 'mouseenter'){
                        const color = '#cecece';
                        $($_el.find('.table__td.hoverable').get(col)).css({ background: color });
                        $($tr.get(row)).css({ background: color});
                        
                    }
                    else{
                        $($_el.find('.table__td.hoverable').get(col)).css({ backgroundColor: 'transparent' });
                        $($tr.get(row)).css({ background: 'transparent'});
                    }
                });
            });








        });
        
    </script>
@endpush