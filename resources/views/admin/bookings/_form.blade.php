<div id="infowindow-content">
    <span id="place-name" class="title"></span><br />
    <span id="place-address"></span>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('region_from', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="region_from">Место отправки</label>
            <div class="">
                <input type="text" name="region_from" class="form-control input-sm" id="region_from" value="{{ old('region_from', $booking->region_from ?? '') }}" required >
                <input type="hidden" name="region_from_id" id="region_from_id" value="{{ old('region_from_id', $booking->region_from_id ?? '') }}">
                <input type="hidden" name="region_from_lat" id="region_from_lat" >
                <input type="hidden" name="region_from_lng" id="region_from_lng" >
                {!! $errors->first('region_from', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('region_to', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="region_to">Место доставки</label>
            <div class="">
                <input type="text" name="region_to" class="form-control input-sm" id="region_to" value="{{ old('region_to', $booking->region_to ?? '') }}" required >
                <input type="hidden" name="region_to_id"  id="region_to_id" value="{{ old('region_to_id', $booking->region_to_id ?? '') }}">
                <input type="hidden" name="region_to_lat" id="region_to_lat" >
                <input type="hidden" name="region_to_lng" id="region_to_lng" >
                {!! $errors->first('region_to', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="distance" id="distance" value="{{ old('distance', $booking->distance ?? 0) }}">
<hr>
<h5 >Габариты и вес</h5>
<div class="row">
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('dimension_x', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="dimension_x">Длина (м)</label>
            <div class="">
                <input type="number" min="0.01" step="any" name="dimension_x" class="form-control input-sm" id="dimension_x" value="{{ old('dimension_x', $booking->dimension_x ?? '') }}" required >
                {!! $errors->first('dimension_x', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('dimension_y', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="dimension_y">Ширина (м)</label>
            <div class="">
                <input type="number" min="0.01" step="any" name="dimension_y" class="form-control input-sm" id="dimension_y" value="{{ old('dimension_y', $booking->dimension_y ?? '') }}" required >
                {!! $errors->first('dimension_y', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('dimension_z', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="dimension_z">Высота (м)</label>
            <div class="">
                <input type="number" min="0.01" step="any" name="dimension_z" class="form-control input-sm" id="dimension_z" value="{{ old('dimension_z', $booking->dimension_z ?? '') }}" required >
                {!! $errors->first('dimension_z', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('weight', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="weight">Вес (кг)</label>
            <div class="">
                <input type="number" min="0.01" step="any" name="weight" class="form-control input-sm" id="weight" value="{{ old('weight', $booking->weight ?? '') }}" required >
                {!! $errors->first('weight', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>
</div>
<hr>

<div class="row">

    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('load_type_id', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="load_type_id">Тип погрузки</label>
            <select name="load_type_id" id="load_type_id" class="form-control" required>
                @foreach(\App\Domain\LoadTypes\Models\LoadType::all() as $loadType)
                    <option value="{{$loadType->id}}" {{ old('load_type_id', $booking->load_type_id ?? '') == $loadType->id ? 'selected': '' }}>{{ $loadType->title }}</option>
                @endforeach
            </select>
            <div class="">
                {!! $errors->first('load_type_id', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('cargo_type_id', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="cargo_type_id">Тип груза</label>
            <select name="cargo_type_id" id="cargo_type_id" class="form-control" required>
                @foreach(\App\Domain\CargoTypes\Models\CargoType::all() as $cargoType)
                    <option value="{{$cargoType->id}}" {{ old('cargo_type_id', $booking->cargo_type_id ?? '') == $cargoType->id ? 'selected': '' }}>{{ $cargoType->title }}</option>
                @endforeach
            </select>
            <div class="">
                {!! $errors->first('cargo_type_id', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group  {!! $errors->first('date_time', 'has-danger')!!}">
            <label class=" text-md-right text-secondary" for="date_time">Время отправки</label>
            <div class="">
                <input type="datetime-local"   name="date_time" class="form-control input-sm" id="date_time" value="{{ old('date_time', $booking->date_time ?? date('Y-m-d\TH:i')) }}" required >
                {!! $errors->first('date_time', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
    </div>

{{--    <div class="col-md-3">--}}
{{--        <div class="form-group  {!! $errors->first('payment_type', 'has-danger')!!}">--}}
{{--            <label class=" text-md-right text-secondary" for="payment_type">Тип оплаты</label>--}}
{{--            <select name="payment_type" id="payment_type" class="form-control" required>--}}
{{--                    <option value="cash" {{ old('payment_type', $booking->payment_type ?? '') == 'cash' ? 'selected': '' }}>Наличными</option>--}}
{{--                    <option value="terminal" {{ old('payment_type', $booking->payment_type ?? '') == 'terminal' ? 'selected': '' }}>Картой</option>--}}
{{--                    <option value="company" {{ old('payment_type', $booking->payment_type ?? '') == 'company' ? 'selected': '' }}>Перечислением</option>--}}
{{--            </select>--}}
{{--            <div class="">--}}
{{--                {!! $errors->first('payment_type', '<small class="form-control-feedback">:message</small>') !!}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

</div>

@push('styles')
    <style>
        #infowindow-content .title {
            font-weight: bold;
        }

        #infowindow-content {
            display: none;
        }

        #map #infowindow-content {
            display: inline;
        }
    </style>
@endpush

@push('scripts')
    <script>
        var regionFromId = null;
        var regionToId = null;

            function initMap() {
                const inputRegionFrom = document.getElementById("region_from");
                const inputRegionTo = document.getElementById("region_to");
                const options = {
                    fields: ["formatted_address", "geometry", "name"],
                    strictBounds: false,
                    types: [],
                };
                const autocompleteRegionFrom = new google.maps.places.Autocomplete(inputRegionFrom, options);
                const autocompleteRegionTo = new google.maps.places.Autocomplete(inputRegionTo, options);

                // Bind the map's bounds (viewport) property to the autocomplete object,
                // so that the autocomplete requests use the current map bounds for the
                // bounds option in the request.
                //autocomplete.bindTo("bounds", map);

                const infowindow = new google.maps.InfoWindow();
                const infowindowContent = document.getElementById("infowindow-content");

                infowindow.setContent(infowindowContent);

                autocompleteRegionFrom.addListener("place_changed", () => {
                    infowindow.close();
                    const place = autocompleteRegionFrom.getPlace();

                    if (!place.geometry || !place.geometry.location) {
                        // User entered the name of a Place that was not suggested and
                        // pressed the Enter key, or the Place Details request failed.
                        return;
                    }

                    @foreach($regions as $region)
                        if(google.maps.geometry.poly.containsLocation(
                            place.geometry.location,
                        new google.maps.Polygon({ paths: {!! $region->polygon !!} })
                        )) {
                        regionFromId = {{ $region->id }}
                        }
                    @endforeach

                    // console.log(place.geometry.location.lat(), place.geometry.location.lng());
                    $('#region_from_id').val(regionFromId);
                    $('#region_from_lat').val(place.geometry.location.lat());
                    $('#region_from_lng').val(place.geometry.location.lng());
                    getDistance();

                    //place.geometry.location

                    infowindowContent.children["place-name"].textContent = place.name;
                    infowindowContent.children["place-address"].textContent =
                        place.formatted_address;
                    //infowindow.open(map, marker);
                });

                autocompleteRegionTo.addListener("place_changed", () => {
                    infowindow.close();
                    const place = autocompleteRegionTo.getPlace();

                    if (!place.geometry || !place.geometry.location) {
                        // User entered the name of a Place that was not suggested and
                        // pressed the Enter key, or the Place Details request failed.
                        return;
                    }

                    @foreach($regions as $region)
                    if(google.maps.geometry.poly.containsLocation(
                        place.geometry.location,
                        new google.maps.Polygon({ paths: {!! $region->polygon !!} })
                    )) {
                        regionToId = {{ $region->id }}
                    }
                    @endforeach

                    // console.log(place.geometry.location.lat(), place.geometry.location.lng());
                    $('#region_to_id').val(regionToId);
                    $('#region_to_lat').val(place.geometry.location.lat());
                    $('#region_to_lng').val(place.geometry.location.lng());
                    getDistance();

                    infowindowContent.children["place-name"].textContent = place.name;
                    infowindowContent.children["place-address"].textContent =
                        place.formatted_address;
                    //infowindow.open(map, marker);
                });





                // function getDistance() {
                //     var origins_lat = $('#region_from_lat').val();
                //     var origins_lng = $('#region_from_lng').val();
                //     var destinations_lat = $('#region_to_lat').val();
                //     var destinations_lng = $('#region_to_lng').val();
                //     if(origins_lat != '' && origins_lng != '' && destinations_lat != '' && destinations_lng != '') {
                //
                //
                //         console.log(origins_lat,origins_lng, destinations_lat, destinations_lng );
                //
                //         const service = new google.maps.Directions();
                //         // build request
                //         const origin1 = { lat: origins_lat, lng: origins_lng };
                //         const destinationB = { lat: destinations_lat, lng: destinations_lng };
                //         const request = {
                //             origins: [origin1],
                //             destinations: [destinationB],
                //             travelMode: google.maps.TravelMode.DRIVING,
                //             unitSystem: google.maps.UnitSystem.METRIC,
                //             avoidHighways: false,
                //             avoidTolls: false,
                //         };
                //
                //         // get distance matrix response
                //         service.getDistanceMatrix(request).then((response) => {
                //             // put response
                //             console.log(JSON.stringify(response.data));
                //         });
                //     }
                // }
            }

        function getDistance() {
            var origins_lat = $('#region_from_lat').val();
            var origins_lng = $('#region_from_lng').val();
            var destinations_lat = $('#region_to_lat').val();
            var destinations_lng = $('#region_to_lng').val();
            if(origins_lat != '' && origins_lng != '' && destinations_lat != '' && destinations_lng != '') {


                //console.log(origins_lat,origins_lng, destinations_lat, destinations_lng );

                const directionsService = new google.maps.DirectionsService();
                // get distance matrix response
                directionsService
                    .route({
                        origin: {
                            query: origins_lat + ',' + origins_lng,
                        },
                        destination: {
                            query: destinations_lat + ',' + destinations_lng,
                        },
                        travelMode: google.maps.TravelMode.DRIVING,
                    })
                    .then((response) => {
                        if(response.routes != null && response.routes[0] != null && response.routes[0].legs != null && response.routes[0].legs[0] != null) {
                            if(response.routes[0].legs[0].distance != null && response.routes[0].legs[0].distance.value != null) {
                                var distance = response.routes[0].legs[0].distance.value * 1 / 1000;

                                $('#distance').val(parseInt(distance));
                                console.log(parseInt(distance));
                            }
                        }
                    })
                    .catch((e) => {
                        console.log(e);
                    });
            }
        }

            $('#region_from').on('change', function() {
                regionFromId = null
            });

            $('#region_to').on('change', function() {
                regionToId = null
            });

            $('#create_from').on('submit', function () {
                if(regionFromId != null && regionToId != null) {
                } else {
                    alert("Указаны не верные регионы");
                    return false;
                }
            });

        // $('#submit_button').on('click', function () {
        //     if(regionFromId != null && regionToId != null) {
        //     } else {
        //         if (regionFromId == null) {
        //             $('#region_from').val('');
        //         }
        //
        //         if (regionFromId == null) {
        //             $('#region_to').val('');
        //         }
        //     }
        // });

        // function getDistance()
        // {
        //     var origins_lat = $('#region_from_lat').val();
        //     var origins_lng = $('#region_from_lng').val();
        //     var destinations_lat = $('#region_to_lat').val();
        //     var destinations_lng = $('#region_to_lng').val();
        //     if(origins_lat != '' && origins_lng != '' && destinations_lat != '' && destinations_lng != '') {
        //         $.ajax({
        //             url: "https://maps.googleapis.com/maps/api/directions/json?origin=" + origins_lat +','+ origins_lng + "&destination=" + destinations_lat +','+ destinations_lng + "&key=AIzaSyCznuzbAZVG5l0JDT-Cj2v5EvTmbG0pI5U",
        //             context: document.body,
        //         }).done(function(response) {
        //             console.log(JSON.stringify(response.data));
        //         });
        //     }
        // }

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCznuzbAZVG5l0JDT-Cj2v5EvTmbG0pI5U&callback=initMap&libraries=geometry,places"></script>
@endpush
