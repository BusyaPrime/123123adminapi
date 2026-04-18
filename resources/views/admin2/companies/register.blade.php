
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="csrf-token" content="HqpigafpUmhR5CSM5WMiXpVmmjlMODffuxmhRLrl">
    
    <title>@yield('title', config('app.name'))</title>

    <link rel="icon" type="image/png"   href="{{ asset('casva/img/logo.png') }}">

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('casva/dist/css/adminlte.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('casva/plugins/summernote/summernote-bs4.min.css') }}">

    <link href="{{ asset('css/adminlte.css') }}" rel="stylesheet">
    <link href="{{ asset('css/core.css') }}" rel="stylesheet">
	<style>
	.input-append.input-group input.password {
    border-right: 0;
}

.input-append.input-group {
    display: flex;
}

.input-append.input-group span.add-on.input-group-addon {
    background: transparent;
    border: 1px solid #c4c4c4;
    border-left: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    border-radius: 0 .5rem .5rem 0;
}

label.text-md-right.col-form-label-sm {
    color: #6c757d !important;
}
input.form-control {
    border-radius: .5rem;
}

.custom-file-input {
    min-width: 14rem;
    max-width: 100%;
    height: calc(2.25rem + 2px);
    margin: 0;
opacity: 0;}

 .custom-file {
    border: 0;
    height: 2.5rem;
    position: relative;
    width: 100%;
}

.custom-file-input:lang(en)~.custom-file-label::after { display:none; }

.custom-file input.form-control.custom-file-input {
    width: 100%;
    float: left;
    border: none !important;
    height: 100%;
    position: absolute;
}

.custom-file label.custom-file-label {
    width: 100% !important;
    float: left;
    height: 100%;
    margin-bottom: 0 !important;
    border: 1px solid #bfbfbf;
    background: #fff;
    padding: 0.28rem 0.57rem;
    border-radius: .5rem;
    position: relative;
    display: flex;
    align-items: center;
    color: #7f7f7f;
}

.custom-file label.custom-file-label:before {
    content: 'Choose File';
    width: auto;
    background: #a6a6a6;
    color: #000;
    padding: 3px 8px;
    border-radius: .5rem;
    margin-right: 10px;
    margin-left: -4px;
}

h5 { color:#000; }
.text-secondary {
    color: #000 !important;
    padding: 0 !important;
font-weight: 400 !important;}


 .form-group .extra_height input.form-control {
height: 3.5rem;}

 .custom-file label.custom-file-label > font {
    overflow: hidden;
    text-overflow: ellipsis !important;
    white-space: nowrap;
    width: calc(100% - 100px);
}
.form-group label.text-md-right { font-size:1rem; font-weight:500; }
.form-group label.text-md-right, .small.text-muted { color:#000 !important; }
 .card-body h5 {
    color: #000;
 font-weight: 600;
}

@media(max-width:992px){
 .card-body .col-sm-3, .row .col-md-3 {
    max-width: 100%;
    float: left;
    width: 33.33%;
    flex: inherit;
}

}

@media(max-width:767px){
 .card-body .col-sm-3, .row .col-md-3 {
    max-width: 100%;
    float: left;
    width: 50%;
flex: inherit;
}
.custom_register form section.content-header .container-fluid .row.mb-2.card-header .col-sm-12 h1 {
    font-size: 2rem;
}


}

@media(max-width:500px){
 .card-body .col-sm-3, .row .col-md-3 {
    max-width: 100%;
    float: left;
    width: 100%;
    flex: inherit;
}
}


	</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed" style="background: #fff;">

<div class="wrapper">
@if (session('success'))
<div class="alert alert-success" id="successMessage">
	{{ session()->get('success') }}
</div>
@endif
@if (session('danger'))
<div class="alert alert-danger" id="dangerMessage">
	{{ session()->get('danger') }}
</div>
@endif
    <form action="{{ route('admin2.companies.companystore') }}" method="post" enctype="multipart/form-data">
        @csrf
        @component('component.card', ['title' => "Добавить логистическую компанию / мерчант", 'title_class' => 'col-sm-12'])
            @include('admin2.companies.register_form')
            @slot('bottom')
                <div class="px-5 py-3">
                <!--<a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>-->
                <button class="btn btn-sm btn-primary float-right submit-btn">@lang('admin.create')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
</div>
<!-- jQuery -->
<script src="{{ asset('casva/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('casva/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<link href="https://merchant.casva.uz/vendor/font-iconmoon/style.css" rel="stylesheet">
<!-- Bootstrap 4 -->
<script src="{{ asset('casva/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('casva/plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('casva/plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('casva/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('casva/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('casva/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('casva/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('casva/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('casva/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('casva/plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('casva/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('casva/dist/js/adminlte.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('casva/dist/js/demo.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('casva/dist/js/pages/dashboard.js') }}"></script>

<script>
setTimeout(function() {
    $(window).scrollTop(0);
}, 1000);
setTimeout(function() {
    $('#successMessage').fadeOut('fast');
    $('#dangerMessage').fadeOut('fast');
}, 5000);
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
            src="https://admin.casva.uz/vendor/inputmask/jquery.inputmask.bundle.js"></script>
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
<script type="text/javascript" src="{{ asset('js/core.js') }}"></script>
<script type="text/javascript"
            src="https://merchant.casva.uz/vendor/bootstrap-show-password/bootstrap-show-password.min.js"></script>
    <script>
        $(function () {
            $('.password').password({
                eyeClass: '',
                eyeOpenClass: 'icmn-eye',
                eyeCloseClass: 'icmn-eye-blocked'
            });
        });
    </script>
<script>
// Add the following code if you want the name of the file appear on select
$("#logo").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".logolabel").addClass("selected").html('<font>'+fileName+'</font>');
});

$("#certificate").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".certificatelabel").addClass("selected").html('<font>'+fileName+'</font>');
});

$("#licence").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".licencelabel").addClass("selected").html('<font>'+fileName+'</font>');
});

$("#agreement").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".agreementlabel").addClass("selected").html('<font>'+fileName+'</font>');
});
</script>
</body>
</html>


