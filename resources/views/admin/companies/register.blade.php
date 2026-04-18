
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="csrf-token" content="HqpigafpUmhR5CSM5WMiXpVmmjlMODffuxmhRLrl">
    
    <title>Casva</title>

    <link rel="icon" type="image/png"   href="https://admin.tst.casva.uz/casva/img/logo_favicon.png">

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://admin.tst.casva.uz/casva/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="https://admin.tst.casva.uz/casva/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="https://admin.tst.casva.uz/casva/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="https://admin.tst.casva.uz/casva/plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://admin.tst.casva.uz/casva/dist/css/adminlte.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="https://admin.tst.casva.uz/casva/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="https://admin.tst.casva.uz/casva/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="https://admin.tst.casva.uz/casva/plugins/summernote/summernote-bs4.min.css">

        <link href="https://admin.tst.casva.uz/css/adminlte.css" rel="stylesheet">
    <link href="https://admin.tst.casva.uz/css/core.css" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">
    <form action="{{ route('admin.companies.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @component('component.card', ['title' => "Добавить логистическую компанию / мерчант", 'title_class' => 'col-sm-12'])
            @include('admin.companies.register_form')
            @slot('bottom')
                <div class="px-5 py-3">
                <!--<a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>-->
                <button class="btn btn-sm btn-primary float-right">@lang('admin.create')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
</div>
<!-- jQuery -->
<script src="https://admin.tst.casva.uz/casva/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://admin.tst.casva.uz/casva/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="https://admin.tst.casva.uz/casva/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="https://admin.tst.casva.uz/casva/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="https://admin.tst.casva.uz/casva/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="https://admin.tst.casva.uz/casva/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="https://admin.tst.casva.uz/casva/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="https://admin.tst.casva.uz/casva/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="https://admin.tst.casva.uz/casva/plugins/moment/moment.min.js"></script>
<script src="https://admin.tst.casva.uz/casva/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="https://admin.tst.casva.uz/casva/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="https://admin.tst.casva.uz/casva/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="https://admin.tst.casva.uz/casva/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="https://admin.tst.casva.uz/casva/dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="https://admin.tst.casva.uz/casva/dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="https://admin.tst.casva.uz/casva/dist/js/pages/dashboard.js"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        // beforeSend: function(){
        //     $("#preloader").show();
        // },
        // complete: function(){
        //     $("#preloader").hide();
        // }
    });
</script>
    <script type="text/javascript"
            src="https://admin.tst.casva.uz/vendor/inputmask/jquery.inputmask.bundle.js"></script>
    <script>
        $(function () {
            $('.phone-input').inputmask("+\\9\\9\\8 (99) 999-99-99");
        });
    </script>
    <script>
        $(function () {
            $('#phones').on('click', '.remove_phone', function () {
                                if($('#phones').find('.input-group').length > 1) {
                    $(this).parent().remove();
                } else {
                    $('#phones').find('input').val('');
                }
                            });
            $('.add_phone').on('click', function () {
                if($('#phones').find('.input-group').length > 0) {
                    var copyElem = $($('#phones').find('.input-group')[0]).clone();
                    copyElem.find('input').val('');

                                        copyElem.find('input').inputmask('+\\9\\9\\8 (99) 999-99-99');
                    
                    $('#phones').append(copyElem);
                }
            });
        });
    </script>
    <script>
        $(function () {
            $('#emails').on('click', '.remove_email', function () {
                                if($('#emails').find('.input-group').length > 1) {
                    $(this).parent().remove();
                } else {
                    $('#emails').find('input').val('');
                }
                            });
            $('.add_email').on('click', function () {
                if($('#emails').find('.input-group').length > 0) {
                    var copyElem = $($('#emails').find('.input-group')[0]).clone();
                    copyElem.find('input').val('');

                    
                    $('#emails').append(copyElem);
                }
            });
        });
    </script>
<script type="text/javascript" src="https://admin.tst.casva.uz/js/core.js"></script>
</body>
</html>


