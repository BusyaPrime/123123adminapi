<!DOCTYPE html>
<html>
<head>
    <title>Simple Map</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <style>
        #map {
            height: 100%;
        }

        /* Optional: Makes the sample page fill the window. */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>

    <script>
        let map;

        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 6,
                center: { lat: 41.311081, lng: 69.240562 },
                mapTypeId: 'roadmap'
            });
            @foreach($regions as $i => $region)
{{--            @if($region->id == 11)--}}
            const polygon{{$i}} = new google.maps.Polygon({
                paths: {!! $region->polygon !!},
                strokeColor: "#000000",
                strokeOpacity: 0,
                strokeWeight: 2,
                fillColor: "#{{mt_rand(100000, 999999)}}",
                fillOpacity: 0.35,
            });
            polygon{{$i}}.setMap(map);

            {{--const polygonEditable{{$i}} = new google.maps.Polygon({--}}
            {{--    paths: [{--}}
            {{--        lat: 39.767945,--}}
            {{--        lng: 64.421701--}}
            {{--    },{--}}
            {{--        lat: 39.767945,--}}
            {{--        lng: 64.421701--}}
            {{--    },{--}}
            {{--        lat: 39.767945,--}}
            {{--        lng: 64.421701--}}
            {{--    }],--}}
            {{--    strokeColor: "#000000",--}}
            {{--    strokeOpacity: 0,--}}
            {{--    strokeWeight: 2,--}}
            {{--    editable: true,--}}
            {{--    fillColor: "#{{mt_rand(100000, 999999)}}",--}}
            {{--    fillOpacity: 0.35,--}}
            {{--});--}}
            {{--polygonEditable{{$i}}.setMap(map);--}}
            {{--polygonEditable{{$i}}.addListener("dragend", function () {--}}
            {{--    console.log(polygonEditable{{$i}}.getPath().getArray())--}}
            {{--});--}}
            {{--google.maps.event.addListener(polygonEditable{{$i}}.getPath(), 'set_at', function() {--}}
            {{--    var MVCarray = polygonEditable{{$i}}.getPath().getArray();--}}

            {{--    var to_return = MVCarray.map(function(point) {--}}
            {{--        return `{"lat":${point.lat()},"lng":${point.lng()}}`;--}}
            {{--    });--}}
            {{--    // first and last must be same--}}
            {{--    console.log(to_return.concat(to_return[0]).join(","));--}}
            {{--});--}}
            {{--@endif--}}
            @endforeach
        }
    </script>
</head>
<body>
<div id="map"></div>

<!-- Async script executes immediately and must be after any DOM elements used in callback. -->
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBXTw2-ElTLmCHWxNSNHYRCbMmrr3PbN3k&callback=initMap&libraries=&v=weekly"
    async
></script>
</body>
</html>
