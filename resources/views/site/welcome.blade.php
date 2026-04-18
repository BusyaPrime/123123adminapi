<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
    <div id="app"></div>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        Echo.channel('casva_database_truck.6') //25
            .listen('.SearchEvent', (e) => {
                console.log(e);
            });
        Echo.channel('casva_database_truck_order.172')
            .listen('.OrderStatusEvent', (e) => {
                console.log(e);
            });
    </script>
    </body>
</html>
