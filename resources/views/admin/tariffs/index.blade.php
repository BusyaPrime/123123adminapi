@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => "Тарифы (карта)", 'bodyClass' => 'card-body-no-padding'])
    <div class="row">
        <div class="col">

        {{-- <div id="map" class="w-100" style='height:calc(60vh - 250px);'></div> --}}

        @push('scripts')
            <script src="https://api-maps.yandex.ru/2.1/?apikey=858338d6-1dfc-4820-8d60-d6582151839e&lang=ru_RU" type="text/javascript">
            </script>
            <script>
                $(function (){
                    ymaps.ready(init);

                    function init() {
                        var myMap = new ymaps.Map("map", {
                            center: [41.326681, 69.244031],
                            zoom: 6
                        }, {
                            searchControlProvider: 'yandex#search'
                        });

                        const polygons = JSON.parse('{!! json_encode($polygons) !!}');
                        const countries = {!! $regions !!};
                        let i = 0;
                        const fillColors = [
                            '#fe0032',
                            '#001144',
                            '#cc1199',
                            '#ff1122',
                            '#ee1122',
                            '#ffaacc',
                            '#cceece',
                            '#192800',
                            '#550019',
                            '#990011',
                            '#990011',
                            '#556011',
                            '#119900',
                            '#001122',
                        ];
                        polygons.map(p => {
                            const regionName = countries[i].title;
                            
                            const polygon = new ymaps.Polygon(
                                p,
                                {
                                    hintContent: countries[i].title,
                                    ballonContent: countries[i].title
                                }, {
                                    fillColor: fillColors[i],
                                    strokeColor:'#000',
                                    strokeWidth: 2,
                                    opacity: 0.7
                                }
                            );
                            myMap.geoObjects.add(polygon);
                            i++;
                        });
                    }
                });
            </script>
        @endpush
        </div>
    </div>

    @endcomponent
@endsection
