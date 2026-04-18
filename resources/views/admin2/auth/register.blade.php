@extends('ui.empty')
@section('content')
<style>
.cat__pages__login {
    height: 100vh;
    width: 100%;
    background-position: top center;
    overflow: hidden;
}

.cat__pages__login__block__inner {
    min-width: 100%;
    height: calc(100vh - 13rem);
    overflow: auto;
    /* padding: 15px; */
}

.custom_register .row {
}

.custom_register form {
    width: 100%;
    float: left;
}

.custom_register form section.content-header .container-fluid {
}

.custom_register form section.content-header .container-fluid .row.mb-2.card-header {
    padding: 0;
}


.custom_register form section.content-header .container-fluid .row.mb-2.card-header .col-sm-12 h1 {
    font-size: 2.25rem;
}



.custom_register form section.content .container-fluid .row .col-12 {
    padding: 0;
}

.custom_register form section.content .container-fluid .row .col-12 .card-body {
    padding: .7rem;
}

.custom_register form section.content .container-fluid .row .col-12 .card-body h5 {
    color: #000;
    font-weight: 600;
}

.custom_register form section.content .container-fluid .row .col-12 .card-body .row .col-md-3 .form-group, .custom_register form section.content .container-fluid .row .col-12 .card-body .form-group {
    margin-bottom: .0;
}



.custom_register form section.content .container-fluid .row .col-12 .card-body .row .col-md-3 .form-group label.text-md-right, .custom_register form section.content .container-fluid .row .col-12 .card-body .form-group label.text-md-right {
    color: #000 !important;
    padding: 0 !important;
}

.custom_register form section.content .container-fluid .row .col-12 .card-body .row .col-md-3 .form-group .form-control, .custom_register form section.content .container-fluid .row .col-12 .card-body .form-group input.form-control {
    border:none;
    border-bottom:2px solid;
    background:transparent;
    border-radius:0!important;
    border-color: #bfbfbf;
    height: 2.5rem;
	border-radius: .5rem;
    transition:.7s all;
}

.card-body .form-group input.form-control:focus{
    border-color:orange!important;
}

.custom_register form section.content .container-fluid .row .col-12 .card-body .row .col-md-3 .form-group .form-control.password {
    border-right: 0;
	border-radius:.5rem 0 0 .5rem;
}

.custom_register form section.content .container-fluid .row .col-12 .card-body .row .col-md-3 .form-group .form-control.password ~ span.add-on.input-group-addon {
    border:none;
    border-bottom:2px solid;
    border-color: #bfbfbf;
	background: transparent;
	border-radius:0;
    transition:.7s all;
}

.custom_register form section.content .container-fluid .row .col-12 .card-body .row .col-md-3 .form-group .form-control.password:focus ~ span.add-on.input-group-addon{
    border-color:orange!important;
}


.small.text-muted {
    color: #000 !important;
}

.custom_register form section.content .container-fluid .row .col-12 .card-body .form-group .custom-file {
    border: 0;
    height: 2.5rem;
    position: relative;
    width: 100%;
}

.custom_register form section.content .container-fluid .row .col-12 .card-body .form-group .custom-file input.form-control.custom-file-input {
    width: 100%;
    float: left;
    border: none !important;
    height: 100%;
    position: absolute;
}

.custom_register form section.content .container-fluid .row .col-12 .card-body .form-group .custom-file label.custom-file-label {
    width: 100% !important;
    float: left;
    height: 100%;
    margin-bottom: 0 !important;
    border:none;
    border-bottom: 2px solid #bfbfbf;
    background: transparent;
    padding: 0.28rem 0.57rem;
    /* border-radius: .5rem; */
    position: relative;
    display: flex;
    align-items: center;
    color: #7f7f7f;
}

.custom_register form section.content .container-fluid .row .col-12 .card-body .form-group .custom-file label.custom-file-label:before {
    content: 'Choose File';
    width: auto;
    background: orange;
    color: #000;
    padding: 3px 8px;
    border-radius: .5rem;
    margin-right: 10px;
    margin-left: -4px;
}
.cat__pages__login__block__promo { padding:0; }

.custom_register form section.content .container-fluid .row .col-12 .card-body .form-group .custom-file label.custom-file-label > font {
    overflow: hidden;
    text-overflow: ellipsis !important;
    white-space: nowrap;
    width: calc(100% - 100px);
}

.custom_register form section.content .container-fluid .row .col-12 .card-body .form-group .extra_height input.form-control {
    height: 3.5rem;
}

@media(max-width:992px){
.custom_register form section.content .container-fluid .row .col-12 .card-body .col-sm-3, .custom_register form section.content .container-fluid .row .col-12 .card-body .row .col-md-3 {
    max-width: 100%;
    float: left;
    width: 33.33%;
    flex: inherit;
}

}

@media(max-width:767px){
.custom_register form section.content .container-fluid .row .col-12 .card-body .col-sm-3, .custom_register form section.content .container-fluid .row .col-12 .card-body .row .col-md-3 {
    max-width: 100%;
    float: left;
    width: 50%;
flex: inherit;
}
.custom_register form section.content-header .container-fluid .row.mb-2.card-header .col-sm-12 h1 {
    font-size: 2rem;
}
.cat__pages__login__block__inner {
	height:auto;
	margin-top:20px;
}
.cat__pages__login { overflow:auto; }

}

@media(max-width:500px){
.custom_register form section.content .container-fluid .row .col-12 .card-body .col-sm-3, .custom_register form section.content .container-fluid .row .col-12 .card-body .row .col-md-3 {
    max-width: 100%;
    float: left;
    width: 100%;
    flex: inherit;
}


}
</style>
    <div class="cat__pages__login" style="background-image: url('{{ asset('images/login_bg.jpg') }}');">
        <div class="cat__pages__login__block">
            <div class="row">
                <div class="col-xl-12">
                    <div class="cat__pages__login__block__promo text-white text-center">
                        <h1 class="mb-3">
{{--                            <span>@lang('admin.login_title', ['appName' => config('app.name')])</span>--}}
                            <span>&nbsp;</span>
                        </h1>
{{--                        <p>@lang('admin.login_text')</p>--}}
                        <p class="mb-0">&nbsp;</p>
                    </div>
                    <div class="cat__pages__login__block__inner">
                        <div class="cat__pages__login__block__form custom_register">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (session()->has('success'))
                                        @component('component.alert', ['type' => 'success'])
                                            {{ session('success') }}
                                        @endcomponent
                                    @endif
                                    @if (session()->has('warning'))
                                        @component('component.alert', ['type' => 'warning'])
                                            {{ session('warning') }}
                                        @endcomponent
                                    @endif
                                    @if (count($errors) > 0 || session()->has('danger'))
                                        @component('component.alert', ['type' => 'danger'])
                                            <div>
                                                {{ session('danger') }}
                                            </div>
                                            @foreach ($errors->all() as $error)
                                                <div>{{ $error }}</div>
                                            @endforeach
                                        @endcomponent
                                    @endif
                                </div>
                            </div>
							<form action="{{ route('admin2.companies.store') }}" method="post" enctype="multipart/form-data">
								@csrf
								@component('component.card', ['title' => "Добавить юридическую компанию", 'title_class' => 'col-sm-12'])
									@include('admin2.companies.register_form')
									@slot('bottom')
										<div class="px-5 py-3">
										<!--<a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>-->
										<button class="btn btn-sm btn-primary float-right register-btn">@lang('admin.create')</button>
										<div class="clearfix"></div>
										</div>
									@endslot
								@endcomponent
							</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
setTimeout(function() {
    $(window).scrollTop(0);
}, 1000);
setTimeout(function() {
    $('#successMessage').fadeOut('fast');
    $('#dangerMessage').fadeOut('fast');
}, 5000);

</script>

@endsection
@component('component.password-stacks')@endcomponent