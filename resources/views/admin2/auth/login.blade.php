@extends('ui.empty')
@section('content')
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
                        <p>&nbsp;</p>
                    </div>
                    <div class="cat__pages__login__block__inner">
                        <div class="cat__pages__login__block__form">
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
                            <form id="form-validation" name="form-validation" action="{{ route('admin2.auth.login') }}"
                                  method="POST">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label" for="username">@lang('admin.username')</label>
                                    <input id="username" class="form-control " autofocus required
                                           name="username" type="text" value="{{ old('username', '') }}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="password">@lang('admin.password')</label>
                                    <div class="input-append input-group"><input
                                            class="form-control password"
                                            name="password"  required
                                            id="password"
                                            type="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="remember">
                                                    @lang('admin.remember')
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary mr-3">@lang('admin.enter')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@component('component.password-stacks')@endcomponent
