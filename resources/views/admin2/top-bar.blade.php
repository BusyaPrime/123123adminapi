@extends('ui.top-bar')

@section('top-bar-left')@endsection
@section('top-bar-right')

	@if(isset($is_external) && $is_external == 0)
    <li class="nav-item pt-2">
        @if(isset($company))
            Баланс: {{ number_format($company->balance, 0, '', ' ') }} сум
        @endif
    </li>
	
	@endif

    @if(Route::current()->uri != 'users' && isset($is_external) && $is_external == 1)
        <?php
            $token = null;
            if(isset($company) && $company->user){
                $token = base64_encode(json_encode([
                'user_id' => $company->user->id,
            ]));
            }
        ?>
        <li class="nav-item">
        <a href="https://casva.uz/login/{{$token}}" target='_blank' class="btn-success" style="padding:4px 15px;margin-top:5px;margin-right:20px;border-radius:25px;display:inline-block;">Разместить заказ</a>
        </li>
    @endif
    
    <li class="nav-item">
        <div class="dropdown nav-link">
            <a href="#" class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{ asset('casva/dist/img/user-icon.png') }}" alt="User">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('admin2.profile.edit')}}" class="dropdown-item"><i class="dropdown-icon icmn-user"></i> @lang('admin.profile')</a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('admin2.auth.logout') }}" class="dropdown-item text-danger"><i
                        class="dropdown-icon icmn-exit"></i> @lang('admin.logout')</a>
            </div>
        </div>
    </li>
@endsection
