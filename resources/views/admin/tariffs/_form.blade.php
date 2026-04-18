<div class="row regions-container">

@push('styles')
        <style>
            ._search{
                width:100%;
            }
            
            #search-directions{
                width:100%;
                padding:6px 15px;
                border: 1px solid #cacaca;
                border-radius:6px;
                color:#000;
                outline:none;
                transition: .2s all;
            }

            input::placeholder{
                opacity:.4;
                color:#000;
            }

            input{
                outline:none;
            }

            img{
                width: 100%;
            }

            #search-directions:focus, #search-directions:hover{
                border-color:var(--orange);
            }

            .region-selector-container{
                background: #fcfcfc;
                padding:0 25px;
                border-radius:10px;
                box-shadow: 0 0 5px #dedede;
                transition:.3s box-shadow;
                margin-bottom:20px;
            }

            .region-selector-container:hover{
                box-shadow: 0 0 15px #dfdfdf;
            }

            .region-selector-title-container{
                display: flex;
                align-items:center;
                cursor:pointer;
                user-select:none;
                padding: 15px 0;
            }

            .selector-indicator{
                width:18px;
                height:18px;
                background:orange;
                display: flex;
                justify-content: center;
                align-items: center;
                color:#fff;
                border-radius:2px;
                margin-right:20px;
                display:none;
            }

            .selector-title{
                font-size:16px;
            }
            
            .tariffs-content{
                /* margin:25px 0 5px 0; */
                padding: 15px 10px;
                display:none;
            }

            /* .tariffs-content.open{
                height: 100%;
                overflow: auto;
            } */


            .car-type{
                display:flex;
                align-items:center;
                user-select:none;
            }

            .car-type label{
                cursor: pointer;
                display: flex;
                align-items: center;
            }
            
            .car-type input{
                padding: 15px;
                cursor:pointer;
            }

            .car-type img{
                width: 35px;
                margin: 0 15px; 
            }

            .discount-value{
                margin-top:10px;
            }

            .discount-value label{
                font-weight:400!important;
            }
            
            .discount-value input{
                margin:0 15px;
                width:100px;
                padding:5px 10px;
            }

            .selector-indicator.active{
                display:flex!important;
            }

            

        </style>

    @endpush

    @push('scripts')

            <script>
                $(function(){
                    let $buttons = $('.selector-button');
                    const $regions = $('.region');
                    const $searchInput = $('form._search').find('input[type="search"]#search-directions');

                    $searchInput.on('keydown', (e) => {
                        let query = e.currentTarget.value;
                        if(query != '' && query.length >= 2){
                            $.ajax({
                                url: "{{ route('admin.regions.filterTariffs') }}",
                                method: 'get',
                                data: ({
                                    _csrf: "{{ csrf_token() }}",
                                    search: query,
                                    season_id: {{ (int)$currentSeasonId }}
                                }),
                            }).done(res => {
                                let $regionContainer = $('.regions-container');
                                let $rs = $('._region');
                                $rs.hide();

                                const data = res.data;

                                data.map(region => {
                                    let $content = $($rs[0]).clone();
                                    $content.find('.from_region').text(region.region_from);
                                    $content.find('.to_region').text(region.region_to);
                                    let $btn = $content.find('.selector-button');
                                    $($btn).on('click', onClickSelector);

                                    let cars = region.cars;

                                    let $tariffContent = $content.find('div.tariffs-content');

                                    $($tariffContent.find("input[name='region_from_id']")).attr('value', region.region_from_id);
                                    $($tariffContent.find("input[name='region_to_id']")).attr('value', region.region_to_id);

                                    let $_carType = $($content.find('.car-type').get(0));
                                    $_cars = $content.find('._cars');
                                    $_cars.empty();
                                    
                                    cars.map(car => {
                                        let $carType = $_carType.clone();
                                        let $checkbox = $carType.find('input[type="checkbox"]');
                                        let $label = $carType.find('label');
                                        let $icon = $carType.find('.car-type-icon');
                                        let $title = $carType.find('.car-type-title');
                                        let $coefficient = $carType.find('.car-type-coefficient');

                                        $checkbox.attr({
                                            value: car.id,
                                            id: `${car.id}_${region.region_from_id}${region.region_to_id}`
                                        });
                                        $label.attr('for', `${car.id}_${region.region_from_id}${region.region_to_id}`);
                                        $icon.attr({
                                            src: car.icon,
                                            title: car.title,
                                            alt: car.title
                                        });
                                        $title.text(car.title);
                                        $coefficient.text(car.common_coefficient);
                                        $_cars.append($carType);
                                    });

                                    $regionContainer.append($content.show());
                                    $buttons = $('.selector-button');
                                });
                            });
                        }
                    });

                    // $searchInput.on('keyup', (e) => {
                    //     let filter = e.currentTarget.value.toUpperCase();
                        
                    //     if(filter == ''){
                    //         $regions.show();
                    //     }

                    //     $regions.each((el, element) => {
                    //         let direction = $(element).attr('data-regions').toUpperCase();
                    //         let search = direction.indexOf(filter);

                    //         if(search == -1){
                    //             $(element).hide(10);
                    //         } else{
                    //             $(element).show(10);
                    //         }

                    //     });

                    // });




                    const onClickSelector = obj => {
                        let $plus = $(obj.currentTarget).find('.selector-indicator.plus-indicator');
                        let $minus = $(obj.currentTarget).find('.selector-indicator.minus-indicator');

                        let $container = $(obj.currentTarget).parent();
                        let $tariffContent = $container.find('div.tariffs-content');

                        // $tariffContent.toggleClass('open');
                        $tariffContent.slideToggle(100);
                        
                        if($plus.hasClass('active')){
                            $plus.removeClass('active');
                            $minus.addClass('active');
                        } else {
                            $plus.addClass('active');
                            $minus.removeClass('active');
                        }
                    }

                    setTimeout(() => {
                        const $activeElement = $("div.region-selector-container#{{ request()->input('changed') }}");

                        $('html, body').stop().animate({
                            scrollTop: $activeElement.offset().top
                        }, 500);
                        $activeElement.find('.selector-button').click();
                    }, 400);
                    $('.selector-button').on('click', onClickSelector);

                });
            </script>

    @endpush


    @foreach($directions as $direction)
        <div class="col-md-12 _region">
            <form action="{{ route('admin.regions.manageTariffsUpdate') }}" method="post">
                @method('put')
                <div class='region-selector-container' id='{{ "ch_".$direction["region_from_id"]."_".$direction["region_to_id"] }}'>
                    <div class="region-selector-title-container selector-button">
                        <span class="selector-indicator plus-indicator active">+</span>    
                        <span class="selector-indicator minus-indicator">-</span>    
                        <div class="selector-title">
                            <span class="from_region">{{ $direction['region_from'] }}</span><span class="small">&nbsp;&nbsp;&nbsp;<i class="fas fa-arrow-right"></i>&nbsp;&nbsp;&nbsp;</span><span class="to_region">{{ $direction['region_to'] }}</span>
                        </div>
                    </div>

                    <!-- <div class="tariffs-content open"> -->
                    <div class="tariffs-content">
                        <div class="_cars">
                            @foreach($carTypes as $carType)
                                <div class="car-type">
                                    <input type="checkbox" name="checkedCars[]" value="{{ $carType->id }}" id="{{ @trim($carType->id).'_'.$direction['region_from_id'].'_'.$direction['region_to_id'] }}">
                                    <label class="_car" for="{{ @trim($carType->id).'_'.$direction['region_from_id'].'_'.$direction['region_to_id'] }}">
                                        <span>
                                            <img class="car-type-icon" src="{{ $carType->imageUrl() }}" alt="{{ $carType->title }}" title="{{ $carType->title }}">
                                        </span>
                                        <span class="car-type-title">
                                            {{ $carType->title }}
                                        </span>
                                        <span style="padding:0 15px;">-</span> 
                                        <span class="car-type-coefficient">
                                            Коэф: {{ App\Domain\CarTypes\Models\CarTypeRate::where('season_id', $currentSeasonId)->where('car_type_id', $carType->id)->where('region_from_id', $direction['region_from_id'])->where('region_to_id', $direction['region_to_id'])->first()->common_coefficient }}
                                        </span>

                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group w-100 discount-value">
                            <label for='percentage_{{ $direction["region_from_id"]."_".$direction["region_to_id"] }}'>Коэффициент общего изменения: </label>
                            <input type="number" name="common_coeff" id='percentage_{{ $direction["region_from_id"]."_".$direction["region_to_id"] }}' step="any" min='0.1' required />
                            
                            <input type="hidden" name="region_from_id" value="{{ $direction['region_from_id'] }}" />
                            <input type="hidden" name="region_to_id" value="{{ $direction['region_to_id'] }}" />
                            <input type="hidden" name="season_id" value="{{ $currentSeasonId }}" />


                            <input type="submit" class="btn btn-success" value='@lang("admin.save")' />
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
    @endforeach

</div>