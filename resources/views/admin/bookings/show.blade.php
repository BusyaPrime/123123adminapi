@extends('admin.layout')

@section('center_content')
					@component('component.card', ['title' => 'Подробнее о заказах', 'bodyClass' => 'card-body-no-padding'])
						<style>
							.OrderDetailsSection {
								display: flex;
								flex-wrap: wrap;
								align-items: center;
							}

							span.title-highlight-text {
								margin-left: 10px;
								padding: 7px 20px;
								width: auto;
								background: #FF9F2F;
								color: #fff;
								border-color: #FF9F2F;
								font-size: 13px;
								font-weight: 500;
								border-radius: 5px;
								position: relative;
								top: -2px;
							}

							.DetailForm_wrapper { margin-top:3px; }
						</style>
						<div class="w-100 float-left OrderDetailsSection d-flex align-items-start">
					@php
	$routes = json_decode($booking->routes, true);
	use App\Domain\TruckBookings\Models\TruckBooking;
	if ($booking->status == 'canceled') {
		$BookingStatus = "canceled";
	}
	/* elseif(!empty($booking->driver_id) && ($booking->status == 'new' || $booking->status == 'order')) */ elseif (!empty($booking->driver_id) && ($booking->last_status_update == NULL)) {
		$BookingStatus = "assigned";
	} elseif (empty($booking->driver_id) && $booking->status == 'order') {
		$BookingStatus = "order";
	} else {
		$BookingStatus = "accepted";
	}
					@endphp
					<h4 class="capitalize">{{ trans('admin.orderDetails') }}  #{{ $booking->id }}@if($BookingStatus != ""),@endif {{ trans('admin.booking_statuses.' . $BookingStatus) }}</h4>
					@if($booking->status != TruckBooking::STATUS_DONE && $booking->status != TruckBooking::STATUS_CANCELED)
						@if (in_array('change_booking_state', $user_permissions))
							<div class="d-inline-block ml-3 mr-3">
								<button class="d-inline-block btn btn-success" type="button" data-toggle="modal" style="padding: 5px 15px; font-size:12px;" data-target="#changeStateModal">Изменить статус</button>
							</div>
						@endif
					@endif
					@if($booking->partial_percentage > 0)
						<span class="title-highlight-text">
							{{ trans('admin.partial_load') }}: {{$booking->partial_percentage}}%
						</span>
					@endif
					<div class="w-100 float-left DetailForm_wrapper">
				{{--		Way in progress: {{ $booking->way_in_progress  }}--}}
				{{--		{{ trans('admin.booking_statuses.waiting') }}--}}
				{{--		Status: {{ $booking->status  }}--}}
				{{--		Statuses: {{ implode(', ', TruckBooking::statuses())  }}--}}
						<div class="order_steps_wrap">
							<ul>
								<li class="{{ $booking->status != TruckBooking::STATUS_ORDER && $booking->status != TruckBooking::STATUS_CANCELED ? "StepDone" : "" }} processing">
									<div class="steps_column">
										<span class="checkCircle">
												<i class="icon">
													<svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M5.51948 10.1677C5.24303 10.1677 4.9804 10.0571 4.78688 9.86359L0.875046 5.95176C0.474187 5.5509 0.474187 4.88741 0.875046 4.48655C1.27591 4.08569 1.9394 4.08569 2.34026 4.48655L5.51948 7.66578L12.6244 0.560898C13.0252 0.160039 13.6887 0.160039 14.0896 0.560898C14.4904 0.961757 14.4904 1.62525 14.0896 2.02611L6.25209 9.86359C6.05857 10.0571 5.79594 10.1677 5.51948 10.1677Z" fill="white"/>
													</svg>

												</i>
											</span>
										<span class="w-100 float-left stes_Title orderStatus">{{ trans('admin.confirmed') }}</span>
									</div>
								</li>

								@foreach($routes as $route)
									@php
		$isDone = false;
		if ($loop->index <= $booking->way_in_progress && $booking->status != TruckBooking::STATUS_ORDER && $booking->status != TruckBooking::STATUS_CANCELED) {
			if (($booking->status == TruckBooking::STATUS_ACCEPTED || $booking->status == TruckBooking::STATUS_PROCESSING) && $loop->index == $booking->way_in_progress)
				$isDone = false;
			else
				$isDone = true;
		}
									@endphp
									<li class="{{ $isDone ? 'StepDone' : '' }} {{
			$booking->status != TruckBooking::STATUS_ORDER &&
			$booking->status != TruckBooking::STATUS_CANCELED &&
			$loop->index < $booking->way_in_progress ? 'processing' : ''
										}}">
										<div class="steps_column">
											<span class="checkCircle"><i class="icon"><svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.51948 10.1677C5.24303 10.1677 4.9804 10.0571 4.78688 9.86359L0.875046 5.95176C0.474187 5.5509 0.474187 4.88741 0.875046 4.48655C1.27591 4.08569 1.9394 4.08569 2.34026 4.48655L5.51948 7.66578L12.6244 0.560898C13.0252 0.160039 13.6887 0.160039 14.0896 0.560898C14.4904 0.961757 14.4904 1.62525 14.0896 2.02611L6.25209 9.86359C6.05857 10.0571 5.79594 10.1677 5.51948 10.1677Z" fill="white"/></svg></i></span>
											<span class="w-100 float-left stes_Title orderStatus">{{ trans('admin.atPoint', ['point' => $booking->is_round_trip == 1 && ($loop->index + 1) == count($routes) ? chr(65) : chr(65 + $loop->index)]) }}</span>
										</div>
									</li>
								@endforeach

								<li class="{{ $booking->status == TruckBooking::STATUS_DONE ? 'StepDone' : ''  }}">
									<div class="steps_column">
											<span class="checkCircle"><i class="icon"><svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.51948 10.1677C5.24303 10.1677 4.9804 10.0571 4.78688 9.86359L0.875046 5.95176C0.474187 5.5509 0.474187 4.88741 0.875046 4.48655C1.27591 4.08569 1.9394 4.08569 2.34026 4.48655L5.51948 7.66578L12.6244 0.560898C13.0252 0.160039 13.6887 0.160039 14.0896 0.560898C14.4904 0.961757 14.4904 1.62525 14.0896 2.02611L6.25209 9.86359C6.05857 10.0571 5.79594 10.1677 5.51948 10.1677Z" fill="white"/></svg></i></span>
										<span class="w-100 float-left stes_Title orderStatus">{{ trans('admin.booking_statuses.done') }}</span>
									</div>
								</li>

							</ul>
							<div class="iconMap">
								<a href="#map">
									<svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_2_125)">
									<path d="M12.7035 10.8748C11.926 10.8748 11.3151 11.4995 11.3151 12.2631C11.3151 13.0267 11.9399 13.6515 12.7035 13.6515C13.4671 13.6515 14.0918 13.0267 14.0918 12.2631C14.0918 11.4995 13.4671 10.8748 12.7035 10.8748Z" fill="#FF9F2F"/>
									<path d="M29.7941 7.47328C28.6279 4.76599 26.0594 3.25269 22.4775 3.25269H10.8431C6.38645 3.25269 2.77673 6.86241 2.77673 11.319V22.9534C2.77673 26.5354 4.29004 29.1038 6.99733 30.2701C7.26112 30.3811 7.56656 30.3117 7.76093 30.1173L29.6414 8.23688C29.8496 8.02862 29.9191 7.72319 29.7941 7.47328ZM14.6194 17.4694C14.0779 17.997 13.3699 18.2469 12.6618 18.2469C11.9538 18.2469 11.2457 17.9831 10.7042 17.4694C9.28811 16.1366 7.73316 14.0124 8.33015 11.4856C8.85772 9.19484 10.8847 8.16746 12.6618 8.16746C14.4389 8.16746 16.4659 9.19484 16.9935 11.4995C17.5766 14.0124 16.0216 16.1366 14.6194 17.4694Z" fill="#FF9F2F"/>
									<path d="M27.0313 28.979C27.3367 29.2845 27.2951 29.7843 26.9202 29.9925C25.6985 30.6728 24.2129 31.0199 22.4775 31.0199H10.8431C10.4405 31.0199 10.2738 30.5479 10.5515 30.2702L18.9372 21.8845C19.2149 21.6069 19.6452 21.6069 19.9229 21.8845L27.0313 28.979Z" fill="#FF9F2F"/>
									<path d="M30.5438 11.319V22.9534C30.5438 24.6889 30.1967 26.1883 29.5164 27.3962C29.3082 27.771 28.8083 27.7988 28.5029 27.5072L21.3945 20.3989C21.1169 20.1212 21.1169 19.6908 21.3945 19.4131L29.7802 11.0275C30.0717 10.7498 30.5438 10.9164 30.5438 11.319Z" fill="#FF9F2F"/>
									</g>
									<defs>
									<clipPath id="clip0_2_125">
									<rect width="33.3205" height="33.3205" fill="white" transform="translate(0 0.476074)"/>
									</clipPath>
									</defs>
									</svg>
								</a>
							</div>
						</div>
						<div class="w-100 float-left orderFormWrap">
							<form>
								<div class="formColumn_wrap">

								  <div class="form_Column">
									  <div class="w-100 float-left formColumnWrap">
										  <h5>{{ trans('admin.shipperDetails') }}</h5>
										  @if($booking->user)
											<div class="inputLabelRowWrap">

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.shipperID') }}</label>
												  <div class="input_wrap inputPassword">
													  <input type="text" class="hs-input"  value="{{ $booking->user_id }}" disabled style="background: transparent">
													  <a href="{{route('admin.users.show', $booking->user_id)}}" target="_blank">
													  <i class="eyeIcon" aria-hidden="false">
															<svg class="eye" width="14" height="9" viewBox="0 0 14 9" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path fill-rule="evenodd" clip-rule="evenodd" d="M6.57222 0.0144359C4.26078 0.158327 2.07918 1.4959 0.339914 3.83554C-0.114217 4.44643 -0.113862 4.55497 0.344343 5.173C2.08003 7.5142 4.26914 8.85101 6.5915 8.98793C9.21995 9.14293 11.7035 7.80125 13.6601 5.16931C14.1142 4.55842 14.1139 4.44988 13.6557 3.83185C12.3877 2.12164 10.8793 0.946624 9.21112 0.369821C8.69291 0.190637 8.0651 0.0597118 7.57409 0.028376C7.03825 -0.00579503 6.92272 -0.00738988 6.57222 0.0144359ZM7.57822 1.38949C8.36393 1.54927 9.11893 2.12663 9.52785 2.88044C10.0213 3.79012 10.0734 4.8905 9.66694 5.82033C9.5083 6.18319 9.35674 6.41919 9.09564 6.7099C7.63431 8.33685 5.14457 7.83944 4.29705 5.75119C3.9912 4.9976 3.98543 4.07704 4.28166 3.29152C4.56985 2.52729 5.19654 1.85814 5.90838 1.55462C6.44972 1.32381 6.99161 1.2702 7.57822 1.38949ZM6.71296 2.81779C6.26839 2.92167 5.8749 3.22126 5.64955 3.62747C5.48389 3.92609 5.42831 4.14781 5.43022 4.50242C5.43211 4.84461 5.46029 4.98262 5.58689 5.26955C5.70324 5.53317 6.04302 5.8991 6.29215 6.02908C7.16944 6.48674 8.18984 6.02122 8.50556 5.01927C8.59589 4.73264 8.5984 4.28328 8.51136 3.98434C8.31264 3.30207 7.73265 2.82665 7.06834 2.80152C6.93302 2.79641 6.7731 2.80374 6.71296 2.81779Z" fill="#FF9F2F"/>
															</svg>


															<svg class="eyeSplash" style="display:none;" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path fill-rule="evenodd" clip-rule="evenodd" d="M13.4101 0.0434427C13.365 0.0669036 12.5957 0.817189 11.7006 1.71073L10.0731 3.33536L9.77284 3.18704C7.45642 2.04282 5.23348 2.31043 2.85539 4.01977C1.94471 4.67438 0.817411 5.77669 0.136962 6.67794C-0.00446036 6.86524 -0.03514 7.01826 0.0392623 7.16493C0.10486 7.29419 0.533747 7.82197 0.892879 8.21537C1.51796 8.90006 2.33707 9.62617 2.99695 10.0805L3.19317 10.2156L1.64844 11.7585C0.798817 12.607 0.0798951 13.347 0.0508287 13.4028C-0.016765 13.5325 -0.0161088 13.645 0.0530162 13.7805C0.114922 13.9019 0.27245 13.9986 0.408129 13.9986C0.45445 13.9986 0.537985 13.9748 0.593794 13.9457C0.72086 13.8795 13.8938 0.7024 13.9544 0.580993C14.1214 0.245841 13.7418 -0.129124 13.4101 0.0434427ZM7.67722 4.21055C7.80264 4.2429 7.99301 4.30554 8.10028 4.34976C8.29904 4.43173 8.69328 4.66276 8.69328 4.69729C8.69328 4.70777 8.56534 4.84386 8.40894 4.99972L8.12459 5.28311L7.97827 5.20077C7.57383 4.97327 7.05889 4.89535 6.5935 4.99121C5.51706 5.21297 4.80082 6.24449 4.97557 7.32139C5.0118 7.54452 5.06597 7.70593 5.18322 7.94004L5.27813 8.12956L4.99693 8.41172C4.84227 8.56693 4.70717 8.69388 4.6967 8.69388C4.66208 8.69388 4.43102 8.2994 4.34869 8.09973C4.18504 7.70289 4.14178 7.46724 4.144 6.9849C4.14564 6.63334 4.15707 6.50696 4.20216 6.34147C4.35318 5.78746 4.60351 5.34285 4.97172 4.97464C5.39672 4.54964 5.98874 4.24713 6.57414 4.15589C6.83965 4.11449 7.41524 4.14304 7.67722 4.21055ZM10.5826 5.1503L9.70016 6.03301L9.75616 6.21427C10.0497 7.16477 9.75589 8.29084 9.02308 9.02368C8.29021 9.75652 7.16417 10.0503 6.21362 9.75674L6.03227 9.70074L5.37471 10.3583C5.01304 10.72 4.72467 11.0234 4.73391 11.0327C4.76626 11.0651 5.3609 11.2641 5.62556 11.3312C6.14991 11.4641 6.39964 11.4932 7.01164 11.4926C7.4822 11.4922 7.64512 11.4813 7.91399 11.4323C8.88945 11.2545 9.84547 10.8514 10.7988 10.2159C11.8036 9.54602 12.8112 8.61358 13.6698 7.55909C14.1036 7.0263 14.1036 6.97085 13.6698 6.43805C13.0372 5.66113 12.0777 4.70818 11.5352 4.31806L11.465 4.26759L10.5826 5.1503ZM7.86266 7.87021C6.98487 8.74819 6.72976 9.01815 6.76506 9.03172C6.91501 9.08925 7.46019 9.02102 7.74686 8.90883C8.32486 8.68265 8.81661 8.12984 8.98108 7.52133C9.03218 7.33233 9.06568 6.83254 9.03136 6.77124C9.01086 6.7346 8.79689 6.93579 7.86266 7.87021Z" fill="#FF9F2F"/>
															</svg>
														  </i>
													</a>
												  </div>
											  </div>

											<div class="inputLabelRow">
												  <label>{{ trans('admin.shipperName') }}</label>
												  <div class="input_wrap">
													  <input type="text" class="hs-input" placeholder="{{ ($booking->user->surname ?? '') . ' ' . ($booking->user->name ?? '') . ' ' . ($booking->user->middle_name ?? '') }}" disabled style="background: transparent">
												  </div>
											  </div>
											@if($booking->clientCompany && $booking->clientCompany->role == \App\Domain\Companies\Models\Company::ROLE_COMPANY)
												<div class="inputLabelRow">
													<label>Компания</label>
													<div class="input_wrap">
														<input type="text" class="hs-input" placeholder="{{ $booking->clientCompany->title }}" disabled style="background: transparent">
													</div>
												</div>
											@endif

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.phoneNumber') }}</label>
												  <div class="input_wrap">
													  @if($booking->user->user && $booking->user->user->is_external == 0)
													  <input type="number" class="hs-input" placeholder="{{ $booking->user->user->username }}" disabled style="background: transparent" />
													@elseif($booking->user->user && $booking->user->user->is_external == 1)
													  <input type="number" class="hs-input" placeholder="+{{ $booking->user->phone_number }}" disabled style="background: transparent" />
													@endif
												  </div>
											  </div>

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.paymentType') }}</label>
												  <div class="input_wrap">
													  <input type="text" class="hs-input" placeholder="{{ trans('admin.payment_types.' . $booking->payment_type) }}" disabled style="background: transparent">
												  </div>
											  </div>
											  <div class="inputLabelRow">
												  <label>{{ trans('admin.whoPays') }}</label>
												  <div class="input_wrap">
													  <input type="text" class="hs-input" placeholder="{{ trans('admin.payers.' . $booking->payer) }}" disabled style="background: transparent">
												  </div>
											  </div>

										  </div>
									@else 
										<div class="inputLabelRowWrap" style="opacity:0.2">

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.shipperID') }}</label>
												  <div class="input_wrap inputPassword">
													  <input type="text" class="hs-input"  value="" disabled style="background: transparent">
													  <a href="{{route('admin.users.show', $booking->user_id)}}" target="_blank">
													  <i class="eyeIcon" aria-hidden="false">
															<svg class="eye" width="14" height="9" viewBox="0 0 14 9" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path fill-rule="evenodd" clip-rule="evenodd" d="M6.57222 0.0144359C4.26078 0.158327 2.07918 1.4959 0.339914 3.83554C-0.114217 4.44643 -0.113862 4.55497 0.344343 5.173C2.08003 7.5142 4.26914 8.85101 6.5915 8.98793C9.21995 9.14293 11.7035 7.80125 13.6601 5.16931C14.1142 4.55842 14.1139 4.44988 13.6557 3.83185C12.3877 2.12164 10.8793 0.946624 9.21112 0.369821C8.69291 0.190637 8.0651 0.0597118 7.57409 0.028376C7.03825 -0.00579503 6.92272 -0.00738988 6.57222 0.0144359ZM7.57822 1.38949C8.36393 1.54927 9.11893 2.12663 9.52785 2.88044C10.0213 3.79012 10.0734 4.8905 9.66694 5.82033C9.5083 6.18319 9.35674 6.41919 9.09564 6.7099C7.63431 8.33685 5.14457 7.83944 4.29705 5.75119C3.9912 4.9976 3.98543 4.07704 4.28166 3.29152C4.56985 2.52729 5.19654 1.85814 5.90838 1.55462C6.44972 1.32381 6.99161 1.2702 7.57822 1.38949ZM6.71296 2.81779C6.26839 2.92167 5.8749 3.22126 5.64955 3.62747C5.48389 3.92609 5.42831 4.14781 5.43022 4.50242C5.43211 4.84461 5.46029 4.98262 5.58689 5.26955C5.70324 5.53317 6.04302 5.8991 6.29215 6.02908C7.16944 6.48674 8.18984 6.02122 8.50556 5.01927C8.59589 4.73264 8.5984 4.28328 8.51136 3.98434C8.31264 3.30207 7.73265 2.82665 7.06834 2.80152C6.93302 2.79641 6.7731 2.80374 6.71296 2.81779Z" fill="#FF9F2F"/>
															</svg>


															<svg class="eyeSplash" style="display:none;" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path fill-rule="evenodd" clip-rule="evenodd" d="M13.4101 0.0434427C13.365 0.0669036 12.5957 0.817189 11.7006 1.71073L10.0731 3.33536L9.77284 3.18704C7.45642 2.04282 5.23348 2.31043 2.85539 4.01977C1.94471 4.67438 0.817411 5.77669 0.136962 6.67794C-0.00446036 6.86524 -0.03514 7.01826 0.0392623 7.16493C0.10486 7.29419 0.533747 7.82197 0.892879 8.21537C1.51796 8.90006 2.33707 9.62617 2.99695 10.0805L3.19317 10.2156L1.64844 11.7585C0.798817 12.607 0.0798951 13.347 0.0508287 13.4028C-0.016765 13.5325 -0.0161088 13.645 0.0530162 13.7805C0.114922 13.9019 0.27245 13.9986 0.408129 13.9986C0.45445 13.9986 0.537985 13.9748 0.593794 13.9457C0.72086 13.8795 13.8938 0.7024 13.9544 0.580993C14.1214 0.245841 13.7418 -0.129124 13.4101 0.0434427ZM7.67722 4.21055C7.80264 4.2429 7.99301 4.30554 8.10028 4.34976C8.29904 4.43173 8.69328 4.66276 8.69328 4.69729C8.69328 4.70777 8.56534 4.84386 8.40894 4.99972L8.12459 5.28311L7.97827 5.20077C7.57383 4.97327 7.05889 4.89535 6.5935 4.99121C5.51706 5.21297 4.80082 6.24449 4.97557 7.32139C5.0118 7.54452 5.06597 7.70593 5.18322 7.94004L5.27813 8.12956L4.99693 8.41172C4.84227 8.56693 4.70717 8.69388 4.6967 8.69388C4.66208 8.69388 4.43102 8.2994 4.34869 8.09973C4.18504 7.70289 4.14178 7.46724 4.144 6.9849C4.14564 6.63334 4.15707 6.50696 4.20216 6.34147C4.35318 5.78746 4.60351 5.34285 4.97172 4.97464C5.39672 4.54964 5.98874 4.24713 6.57414 4.15589C6.83965 4.11449 7.41524 4.14304 7.67722 4.21055ZM10.5826 5.1503L9.70016 6.03301L9.75616 6.21427C10.0497 7.16477 9.75589 8.29084 9.02308 9.02368C8.29021 9.75652 7.16417 10.0503 6.21362 9.75674L6.03227 9.70074L5.37471 10.3583C5.01304 10.72 4.72467 11.0234 4.73391 11.0327C4.76626 11.0651 5.3609 11.2641 5.62556 11.3312C6.14991 11.4641 6.39964 11.4932 7.01164 11.4926C7.4822 11.4922 7.64512 11.4813 7.91399 11.4323C8.88945 11.2545 9.84547 10.8514 10.7988 10.2159C11.8036 9.54602 12.8112 8.61358 13.6698 7.55909C14.1036 7.0263 14.1036 6.97085 13.6698 6.43805C13.0372 5.66113 12.0777 4.70818 11.5352 4.31806L11.465 4.26759L10.5826 5.1503ZM7.86266 7.87021C6.98487 8.74819 6.72976 9.01815 6.76506 9.03172C6.91501 9.08925 7.46019 9.02102 7.74686 8.90883C8.32486 8.68265 8.81661 8.12984 8.98108 7.52133C9.03218 7.33233 9.06568 6.83254 9.03136 6.77124C9.01086 6.7346 8.79689 6.93579 7.86266 7.87021Z" fill="#FF9F2F"/>
															</svg>
														  </i>
													</a>
												  </div>
											  </div>
											  <div class="inputLabelRow">
												  <label>{{ trans('admin.shipperName') }}</label>
												  <div class="input_wrap">
													  <input type="text" class="hs-input" placeholder="" disabled style="background: transparent">
												  </div>
											  </div>

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.phoneNumber') }}</label>
												  <div class="input_wrap">
													  <input type="number" class="hs-input" placeholder="" disabled style="background: transparent" />
												  </div>
											  </div>

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.paymentType') }}</label>
												  <div class="input_wrap">
													  <input type="text" class="hs-input" placeholder="" disabled style="background: transparent">
												  </div>
											  </div>

										  </div>
										@endif
									  </div>
								  </div>


								  <div class="form_Column">
									  <div class="w-100 float-left formColumnWrap {{ $booking->driver ?? 'disabled' }}">
										  <h5>{{ trans('admin.driverDetails') }}</h5>
										  <div class="inputLabelRowWrap">

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.driverID') }}</label>
												  <div class="input_wrap inputPassword">
													  <input type="text" class="hs-input " value="{{ $booking->getDriverCar() !== null ? $booking->driver->user_id : '' }}" disabled style="background: transparent">
													@if($booking->getDriverCar() != null)
													<a href="{{route('admin.cars.show', $booking->getDriverCar()->id)}}" target="_blank">
													@else
													@endif
													  <i class="eyeIcon" style="{{ !$booking->driver ?? 'cursor:initial;'}}" aria-hidden="true">
														  <svg class="eye" width="14" height="9" viewBox="0 0 14 9" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" clip-rule="evenodd" d="M6.57222 0.0144359C4.26078 0.158327 2.07918 1.4959 0.339914 3.83554C-0.114217 4.44643 -0.113862 4.55497 0.344343 5.173C2.08003 7.5142 4.26914 8.85101 6.5915 8.98793C9.21995 9.14293 11.7035 7.80125 13.6601 5.16931C14.1142 4.55842 14.1139 4.44988 13.6557 3.83185C12.3877 2.12164 10.8793 0.946624 9.21112 0.369821C8.69291 0.190637 8.0651 0.0597118 7.57409 0.028376C7.03825 -0.00579503 6.92272 -0.00738988 6.57222 0.0144359ZM7.57822 1.38949C8.36393 1.54927 9.11893 2.12663 9.52785 2.88044C10.0213 3.79012 10.0734 4.8905 9.66694 5.82033C9.5083 6.18319 9.35674 6.41919 9.09564 6.7099C7.63431 8.33685 5.14457 7.83944 4.29705 5.75119C3.9912 4.9976 3.98543 4.07704 4.28166 3.29152C4.56985 2.52729 5.19654 1.85814 5.90838 1.55462C6.44972 1.32381 6.99161 1.2702 7.57822 1.38949ZM6.71296 2.81779C6.26839 2.92167 5.8749 3.22126 5.64955 3.62747C5.48389 3.92609 5.42831 4.14781 5.43022 4.50242C5.43211 4.84461 5.46029 4.98262 5.58689 5.26955C5.70324 5.53317 6.04302 5.8991 6.29215 6.02908C7.16944 6.48674 8.18984 6.02122 8.50556 5.01927C8.59589 4.73264 8.5984 4.28328 8.51136 3.98434C8.31264 3.30207 7.73265 2.82665 7.06834 2.80152C6.93302 2.79641 6.7731 2.80374 6.71296 2.81779Z" fill="#FF9F2F"/>
														</svg>


														  <svg class="eyeSplash" style="display:none;" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" clip-rule="evenodd" d="M13.4101 0.0434427C13.365 0.0669036 12.5957 0.817189 11.7006 1.71073L10.0731 3.33536L9.77284 3.18704C7.45642 2.04282 5.23348 2.31043 2.85539 4.01977C1.94471 4.67438 0.817411 5.77669 0.136962 6.67794C-0.00446036 6.86524 -0.03514 7.01826 0.0392623 7.16493C0.10486 7.29419 0.533747 7.82197 0.892879 8.21537C1.51796 8.90006 2.33707 9.62617 2.99695 10.0805L3.19317 10.2156L1.64844 11.7585C0.798817 12.607 0.0798951 13.347 0.0508287 13.4028C-0.016765 13.5325 -0.0161088 13.645 0.0530162 13.7805C0.114922 13.9019 0.27245 13.9986 0.408129 13.9986C0.45445 13.9986 0.537985 13.9748 0.593794 13.9457C0.72086 13.8795 13.8938 0.7024 13.9544 0.580993C14.1214 0.245841 13.7418 -0.129124 13.4101 0.0434427ZM7.67722 4.21055C7.80264 4.2429 7.99301 4.30554 8.10028 4.34976C8.29904 4.43173 8.69328 4.66276 8.69328 4.69729C8.69328 4.70777 8.56534 4.84386 8.40894 4.99972L8.12459 5.28311L7.97827 5.20077C7.57383 4.97327 7.05889 4.89535 6.5935 4.99121C5.51706 5.21297 4.80082 6.24449 4.97557 7.32139C5.0118 7.54452 5.06597 7.70593 5.18322 7.94004L5.27813 8.12956L4.99693 8.41172C4.84227 8.56693 4.70717 8.69388 4.6967 8.69388C4.66208 8.69388 4.43102 8.2994 4.34869 8.09973C4.18504 7.70289 4.14178 7.46724 4.144 6.9849C4.14564 6.63334 4.15707 6.50696 4.20216 6.34147C4.35318 5.78746 4.60351 5.34285 4.97172 4.97464C5.39672 4.54964 5.98874 4.24713 6.57414 4.15589C6.83965 4.11449 7.41524 4.14304 7.67722 4.21055ZM10.5826 5.1503L9.70016 6.03301L9.75616 6.21427C10.0497 7.16477 9.75589 8.29084 9.02308 9.02368C8.29021 9.75652 7.16417 10.0503 6.21362 9.75674L6.03227 9.70074L5.37471 10.3583C5.01304 10.72 4.72467 11.0234 4.73391 11.0327C4.76626 11.0651 5.3609 11.2641 5.62556 11.3312C6.14991 11.4641 6.39964 11.4932 7.01164 11.4926C7.4822 11.4922 7.64512 11.4813 7.91399 11.4323C8.88945 11.2545 9.84547 10.8514 10.7988 10.2159C11.8036 9.54602 12.8112 8.61358 13.6698 7.55909C14.1036 7.0263 14.1036 6.97085 13.6698 6.43805C13.0372 5.66113 12.0777 4.70818 11.5352 4.31806L11.465 4.26759L10.5826 5.1503ZM7.86266 7.87021C6.98487 8.74819 6.72976 9.01815 6.76506 9.03172C6.91501 9.08925 7.46019 9.02102 7.74686 8.90883C8.32486 8.68265 8.81661 8.12984 8.98108 7.52133C9.03218 7.33233 9.06568 6.83254 9.03136 6.77124C9.01086 6.7346 8.79689 6.93579 7.86266 7.87021Z" fill="#FF9F2F"/>
														</svg>
													  </i>
													  @if($booking->driver)
														</a>
													  @else </span>
													  @endif

												  </div>
											  </div>

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.driverName') }}</label>
												  <div class="input_wrap">
													  <input type="text" class="hs-input" value="{{ $booking->driver ? $booking->driver->surname . ' ' . $booking->driver->name : '' }}" disabled style="background: transparent">
												  </div>
											  </div>

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.phoneNumber') }}</label>
												  <div class="input_wrap">
													  <input type="number" class="hs-input" value="{{ isset($booking->driver->user->username) ? $booking->driver->user->username : '' }}" disabled style="background: transparent">
												  </div>
											  </div>

											  @if($booking->payment_type == 'company')
											<div class="inputLabelRow">
												  <label>{{ trans('admin.driverRating') }}</label>
												  <div class="input_wrap">
													  <div class="row">
														<div class="col-4">
															<input type="text" class="hs-input" value="{{ isset($booking['driver']->rating) ? $booking['driver']->rating : '' }}" disabled style="background: transparent">
														</div>
														<div class="col-8">
															<div class="p-2">
																<b>Сумма для водителя</b></b>
																<b>{{ number_format($booking->driver_price, 0, 0, ' ') }} сум</b>
															</div>
														</div>
													</div>
												  </div>
											  </div>
											@else
											<div class="inputLabelRow">
												  <label>{{ trans('admin.driverRating') }}</label>
												  <div class="input_wrap">
													  <input type="text" class="hs-input" value="{{ isset($booking['driver']->rating) ? $booking['driver']->rating : '' }}" disabled style="background: transparent">
												  </div>
											  </div>
											@endif

										  </div>
									  </div>

								  </div>

								  <div class="form_Column">
									  <div class="w-100 float-left formColumnWrap ShipmentDetails">
										  <h5>{{ trans('admin.shipmentDetails') }}</h5>
										  <div class="inputLabelRowWrap">

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.status') }}</label>
												  <div class="input_wrap formBtn">
													  <div class="formBtnStyle capitalize">
														  <svg width="13" height="3" viewBox="0 0 13 3" fill="none" xmlns="http://www.w3.org/2000/svg">
															<circle cx="1.5" cy="1.5" r="1.5" fill="#FF9F2F"/>
															<circle cx="6.5" cy="1.5" r="1.5" fill="#FF9F2F"/>
															<circle cx="11.5" cy="1.5" r="1.5" fill="#FF9F2F"/>
														</svg>
														{{ trans('admin.booking_statuses.' . $booking->status) }}
													</div>
													  <button type="button" class="formBtnStyle" data-toggle="modal" data-target="#AssignPopup">{{ trans('admin.transitDetails') }}</button>
												  </div>
											  </div>


											  <div class="inputLabelRow">
												  <label>{{ trans('admin.orderTime') }}</label>
												  <div class="input_wrap">
													  <input type="text" class="hs-input" placeholder="{{ $booking->created_at ? $booking->created_at->translatedFormat('j F H:i') : '' }}" disabled style="background: transparent">
												  </div>
											  </div>

											<div class="inputLabelRow">
												  <label>Время обновления</label>
												  <div class="input_wrap">
													  <input type="text" class="hs-input" placeholder="{{ $booking->updated_at ? $booking->updated_at->translatedFormat('j F H:i') : '' }}" disabled style="background: transparent">
												  </div>
											  </div>

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.pickup') }}</label>
												  <div class="input_wrap">
													  <input type="text" class="hs-input" placeholder="{{ $booking->pickup_date ? \Carbon\Carbon::parse($booking->pickup_date)->translatedFormat('j F') : '' }} {{ $booking->pickup_time ? $booking->pickup_time : '' }} " disabled style="background: transparent">
												  </div>
											  </div>

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.receiver') }}</label>
												  <div class="input_wrap">
													  <input type="number" class="hs-input" placeholder="{{ $booking->receiver_phone }}" disabled style="background: transparent">
												  </div>
											  </div>

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.distance') }}</label>
												  <div class="input_wrap">
													  <input type="number" class="hs-input" placeholder="{{ $booking->distance ?? 0}} км" disabled style="background: transparent">
												  </div>
											  </div>

											  <div class="inputLabelRow">
												  <label class="filterIconWrap">{{ trans('admin.address') }}  
													@if($booking->is_round_trip == 1)
													<span class="filterIcon">
													 <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M3.65528 14.3447C3.84567 14.5351 4.15433 14.5351 4.34471 14.3447L7.44715 11.2423C7.63753 11.0519 7.63753 10.7432 7.44715 10.5529C7.25676 10.3625 6.9481 10.3625 6.75772 10.5529L4 13.3106L1.24228 10.5529C1.0519 10.3625 0.743235 10.3625 0.552854 10.5529C0.362474 10.7432 0.362474 11.0519 0.552854 11.2423L3.65528 14.3447ZM3.5125 1L3.5125 14L4.4875 14L4.4875 1L3.5125 1Z" fill="#3D3D3D"/>
													<path d="M14.4197 0.655286C14.2293 0.464905 13.9207 0.464905 13.7303 0.655286L10.6279 3.75772C10.4375 3.9481 10.4375 4.25676 10.6279 4.44715C10.8182 4.63753 11.1269 4.63753 11.3173 4.44715L14.075 1.68943L16.8327 4.44715C17.0231 4.63753 17.3318 4.63753 17.5221 4.44715C17.7125 4.25676 17.7125 3.9481 17.5221 3.75772L14.4197 0.655286ZM14.5625 14L14.5625 1L13.5875 1L13.5875 14L14.5625 14Z" fill="#3D3D3D"/>
													</svg>
													</span>
													@endif
												</label>
												  <div class="input_wrap">
													@foreach($routes as $route)
													@if(isset($route['point']) && $route['point'] != 'to')
														<div class="AddressInput">
															<i class="AddressIcon"><svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_2_485)"><path d="M6 15.333C5.91227 15.3359 5.82498 15.3194 5.74429 15.2849C5.6636 15.2503 5.59147 15.1985 5.533 15.133C4.51655 14.0885 3.56732 12.9806 2.691 11.816C0.931 9.462 3.85975e-07 7.45 3.85975e-07 6C-0.0107082 5.17419 0.152992 4.35542 0.480399 3.59722C0.807806 2.83901 1.29155 2.15844 1.9 1.6C3.02306 0.580066 4.48295 0.0103523 6 0C7.51703 0.0104463 8.97689 0.580147 10.1 1.6C10.7085 2.1584 11.1923 2.83896 11.5197 3.59718C11.8471 4.35539 12.0108 5.17418 12 6C12 7.45 11.069 9.462 9.308 11.817C8.43164 12.9816 7.48241 14.0894 6.466 15.134C6.40754 15.1992 6.33554 15.2507 6.25504 15.2851C6.17454 15.3195 6.08749 15.3358 6 15.333ZM6 4C5.73571 3.99398 5.47294 4.04161 5.22757 4.13999C4.9822 4.23837 4.75932 4.38546 4.57239 4.57239C4.38546 4.75932 4.23837 4.9822 4.13999 5.22757C4.04161 5.47294 3.99399 5.73571 4 6C3.99399 6.26429 4.04161 6.52706 4.13999 6.77243C4.23837 7.0178 4.38546 7.24068 4.57239 7.42761C4.75932 7.61454 4.9822 7.76163 5.22757 7.86001C5.47294 7.95839 5.73571 8.00601 6 8C6.26429 8.00601 6.52706 7.95839 6.77243 7.86001C7.0178 7.76163 7.24068 7.61454 7.42761 7.42761C7.61454 7.24068 7.76163 7.0178 7.86001 6.77243C7.9584 6.52706 8.00601 6.26429 8 6C8.00601 5.73571 7.9584 5.47294 7.86001 5.22757C7.76163 4.9822 7.61454 4.75932 7.42761 4.57239C7.24068 4.38546 7.0178 4.23837 6.77243 4.13999C6.52706 4.04161 6.26429 3.99398 6 4Z" fill="#FFAF2A"/></g><defs><clipPath id="clip0_2_485"><rect width="12" height="15.333" fill="white"/></clipPath></defs></svg></i>
															<div class="hs-input">{{$route['address']}}</div>
														</div>
													@elseif($loop->index == 0)
														<div class="AddressInput">
															<i class="AddressIcon"><svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_2_485)"><path d="M6 15.333C5.91227 15.3359 5.82498 15.3194 5.74429 15.2849C5.6636 15.2503 5.59147 15.1985 5.533 15.133C4.51655 14.0885 3.56732 12.9806 2.691 11.816C0.931 9.462 3.85975e-07 7.45 3.85975e-07 6C-0.0107082 5.17419 0.152992 4.35542 0.480399 3.59722C0.807806 2.83901 1.29155 2.15844 1.9 1.6C3.02306 0.580066 4.48295 0.0103523 6 0C7.51703 0.0104463 8.97689 0.580147 10.1 1.6C10.7085 2.1584 11.1923 2.83896 11.5197 3.59718C11.8471 4.35539 12.0108 5.17418 12 6C12 7.45 11.069 9.462 9.308 11.817C8.43164 12.9816 7.48241 14.0894 6.466 15.134C6.40754 15.1992 6.33554 15.2507 6.25504 15.2851C6.17454 15.3195 6.08749 15.3358 6 15.333ZM6 4C5.73571 3.99398 5.47294 4.04161 5.22757 4.13999C4.9822 4.23837 4.75932 4.38546 4.57239 4.57239C4.38546 4.75932 4.23837 4.9822 4.13999 5.22757C4.04161 5.47294 3.99399 5.73571 4 6C3.99399 6.26429 4.04161 6.52706 4.13999 6.77243C4.23837 7.0178 4.38546 7.24068 4.57239 7.42761C4.75932 7.61454 4.9822 7.76163 5.22757 7.86001C5.47294 7.95839 5.73571 8.00601 6 8C6.26429 8.00601 6.52706 7.95839 6.77243 7.86001C7.0178 7.76163 7.24068 7.61454 7.42761 7.42761C7.61454 7.24068 7.76163 7.0178 7.86001 6.77243C7.9584 6.52706 8.00601 6.26429 8 6C8.00601 5.73571 7.9584 5.47294 7.86001 5.22757C7.76163 4.9822 7.61454 4.75932 7.42761 4.57239C7.24068 4.38546 7.0178 4.23837 6.77243 4.13999C6.52706 4.04161 6.26429 3.99398 6 4Z" fill="#FFAF2A"/></g><defs><clipPath id="clip0_2_485"><rect width="12" height="15.333" fill="white"/></clipPath></defs></svg></i>
															<div class="hs-input">{{$route['address']}}</div>
														</div>
													@else
														<div class="AddressInput">
															<i class="AddressIcon"><svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_2_485)"><path d="M6 15.333C5.91227 15.3359 5.82498 15.3194 5.74429 15.2849C5.6636 15.2503 5.59147 15.1985 5.533 15.133C4.51655 14.0885 3.56732 12.9806 2.691 11.816C0.931 9.462 3.85975e-07 7.45 3.85975e-07 6C-0.0107082 5.17419 0.152992 4.35542 0.480399 3.59722C0.807806 2.83901 1.29155 2.15844 1.9 1.6C3.02306 0.580066 4.48295 0.0103523 6 0C7.51703 0.0104463 8.97689 0.580147 10.1 1.6C10.7085 2.1584 11.1923 2.83896 11.5197 3.59718C11.8471 4.35539 12.0108 5.17418 12 6C12 7.45 11.069 9.462 9.308 11.817C8.43164 12.9816 7.48241 14.0894 6.466 15.134C6.40754 15.1992 6.33554 15.2507 6.25504 15.2851C6.17454 15.3195 6.08749 15.3358 6 15.333ZM6 4C5.73571 3.99398 5.47294 4.04161 5.22757 4.13999C4.9822 4.23837 4.75932 4.38546 4.57239 4.57239C4.38546 4.75932 4.23837 4.9822 4.13999 5.22757C4.04161 5.47294 3.99399 5.73571 4 6C3.99399 6.26429 4.04161 6.52706 4.13999 6.77243C4.23837 7.0178 4.38546 7.24068 4.57239 7.42761C4.75932 7.61454 4.9822 7.76163 5.22757 7.86001C5.47294 7.95839 5.73571 8.00601 6 8C6.26429 8.00601 6.52706 7.95839 6.77243 7.86001C7.0178 7.76163 7.24068 7.61454 7.42761 7.42761C7.61454 7.24068 7.76163 7.0178 7.86001 6.77243C7.9584 6.52706 8.00601 6.26429 8 6C8.00601 5.73571 7.9584 5.47294 7.86001 5.22757C7.76163 4.9822 7.61454 4.75932 7.42761 4.57239C7.24068 4.38546 7.0178 4.23837 6.77243 4.13999C6.52706 4.04161 6.26429 3.99398 6 4Z" fill="#55A84F"/></g><defs><clipPath id="clip0_2_485"><rect width="12" height="15.333" fill="white"/></clipPath></defs></svg></i>
															<div class="hs-input">{{$route['address']}}</div>
														  </div>
													@endif
													@endforeach
												  </div>
											  </div>

											@if ($booking->status == TruckBooking::STATUS_CANCELED)
												<div class="inputLabelRow">
													<label>Причина отмены</label>
													<div class="input_wrap">
														<div class="hs-input">{{ $booking->cancelReason ? $booking->cancelReason->reason : '' }}</div>
													</div>
												</div>
												<div class="inputLabelRow">
													<label>Дополнительно</label>
													<div class="input_wrap">
														<div>{{ $booking->cancel_reason_comment }}</div>
													</div>
												</div>
											@endif

										  </div>
									  </div>
								  </div>

								  <div class="form_Column">
									  <div class="w-100 float-left formColumnWrap">
										  <h5>{{ trans('admin.goodTypeAdditional') }}</h5>
										  @php
											  $bookingCarType = $booking->carType;
											  $bookingCarTypeTitle = $bookingCarType->translations[0]->title ?? $bookingCarType->title ?? '';
											  $bookingCargoTypeTitle = $booking->cargoType->title ?? '';
											  $bookingCommissionRate = $bookingCarType->commission ?? 0;
											  $adminPermissions = json_decode($admin->adminRole->permissions ?? '[]', false) ?? [];
										  @endphp
										  <div class="inputLabelRowWrap">

											  <div class="inputLabelRow">
												  <label>Тип авто</label>
												  <div class="input_wrap formBtn">
													  <input type="text" class="hs-input" placeholder="{{ $bookingCarTypeTitle }}" disabled style="background:transparent">
												  </div>
											  </div>
											  <div class="inputLabelRow">
												  <label>{{ trans('admin.goodType') }}</label>
												  <div class="input_wrap formBtn">
													  <input type="text" class="hs-input" placeholder="{{ $bookingCargoTypeTitle }}" disabled style="background:transparent">
												  </div>
											  </div>
											  <div class="inputLabelRow">
												  <label>{{ trans('admin.goodWeight') }}</label>
												  <div class="input_wrap">
													  <input type="text" class="hs-input" placeholder="{{ $booking->weight }} kg" disabled style="background:transparent">
												  </div>
											  </div>

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.dimensions') }}</label>
												  <div class="input_wrap">
													<?php $Dimensions = $booking->dimension_x . "X" . $booking->dimension_y . "X" . $booking->dimension_z; ?>
													  <input type="text" class="hs-input" placeholder="{{ $booking->dimension_x ? $Dimensions : '' }}" disabled style="background:transparent">
												  </div>
											  </div>

											  <div class="inputLabelRow">
												  <label>{{ trans('admin.comments') }}</label>
												  <div class="input_wrap comment">
													  <div class="pl-3 pr-3" style="background:transparent">{{ $booking->comment }}</div>
												  </div>
											  </div>
											  <div class="inputLabelRow twoColumn_data">
												   <ul>
													   <li>
														<button type="button" style="background:transparent;border:none;" class="formBtnStyle" data-toggle="modal" data-target="#AssignPopup">
															<div class="w-100 float-left columnContent">
																<span class="w-100 float-left textRow ">{{ trans('admin.price') }}</span>
																<span class="w-100 float-left textRow textRowbtn ">{{ number_format($booking->price, 0, " ", " ") }} <sub>{{ trans('admin.sum') }}</sub></span>
																<span class="w-100 float-left textRow ">{{ $booking->distance != 0 ? number_format($booking->price / $booking->distance, 0, " ", " ") : 0 }}  {{ trans('admin.sum') }}/{{ trans('admin.km') }}</span>
															</div>
														</button>
														@if ($booking->recomended_price)
														<br/>
														<br/>
															<div class="w-100 float-left columnContent">
																	<span class="w-100 float-left textRow ">Рекомендованная цена</span>
																	<span class="w-100 float-left textRow textRowbtn ">{{ number_format($booking->recomended_price, 0, " ", " ") }} <sub>{{ trans('admin.sum') }}</sub></span>
															</div>
														@endif
													   </li>

													   {{--<li>
														   <div class="w-100 float-left columnContent">
															   <span class="w-100 float-left textRow ">{{ trans('admin.additional_price') }}</span>
															   <span class="w-100 float-left textRow textRowbtn " style="background-color:#75a99c;color:#fff;">
																{{ number_format($booking->additional_price,0," "," ") }} <sub>{{ trans('admin.sum') }}</sub>
															</span>
															<span class="w-100 small"><b>@lang('messages.push_messages.cause') {{ $booking->additional_price_review ?? 'Нет' }}</b></span>
														   </div>
													   </li>--}}
													   <li>
														   <div class="w-100 float-left columnContent">
															   <span class="w-100 float-left textRow ">{{ trans('admin.comission') }} {{ $bookingCommissionRate }}%</span>
															   <span class="w-100 float-left textRow textRowbtn">{{ number_format($booking->commission, 0, " ", " ") }} <sub>{{ trans('admin.sum') }}</sub></span>
															   <span class="w-100 float-left textRow "></span>
														   </div>
													   </li>
												   </ul>
											  </div>

										  </div>
									  </div>
									  <div class="threeInlineButtonRow">
										@if($booking->status != 'canceled' && $booking->status != 'done')
											<a class="btnStyle" style="background: #FF3B2F;" data-toggle="modal" data-target="#cancelModal">{{ trans('admin.cancelOrder') }}</a>
											<a class="btnStyle" data-toggle="modal" style="background: #FF9F2F;" data-toggle="modal" data-target="#CompanyModal">{{ trans('admin.assignDriver') }}</a>
										@endif

										<a href="{{ route('admin.chat.index-bookings', ['chat_id' => $booking]) }}" class="btnStyle mr-0" style="background: #00275E;">{{ trans('admin.chat') }}</a>
										@if(in_array('additional_price', $adminPermissions))
											<a class="btnStyle" style="background: #75a99c;" data-toggle="modal" data-target="#additionalChargeModal">Изменить цену (клиент)</a>
											&nbsp;
											<a class="btnStyle" style="background: #4d4d4d;" data-toggle="modal" data-target="#changePriceModal">Изменить цену (Водитель)</a>
										@endif
									  </div>
								  </div>


								</div>
							</form>
						</div>
					</div>
				</div>


				<!-- poup section -->
				<div class="modal fade AssignPopupSection" id="AssignPopup" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content">
					  <div class="modal-header">
						  <a href="" class="backIcon">
							  <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M7.56994 13.8201C7.37994 13.8201 7.18994 13.7501 7.03994 13.6001L0.969941 7.53012C0.679941 7.24012 0.679941 6.76012 0.969941 6.47012L7.03994 0.400117C7.32994 0.110117 7.80994 0.110117 8.09994 0.400117C8.38994 0.690117 8.38994 1.17012 8.09994 1.46012L2.55994 7.00012L8.09994 12.5401C8.38994 12.8301 8.38994 13.3101 8.09994 13.6001C7.95994 13.7501 7.75994 13.8201 7.56994 13.8201Z" fill="#292D32"/>
							<path d="M18.4999 7.75H1.66992C1.25992 7.75 0.919922 7.41 0.919922 7C0.919922 6.59 1.25992 6.25 1.66992 6.25H18.4999C18.9099 6.25 19.2499 6.59 19.2499 7C19.2499 7.41 18.9099 7.75 18.4999 7.75Z" fill="#292D32"/>
							</svg>
						  </a>
						<h5 class="modal-title capitalize" id="exampleModalLabel">{{ trans('admin.orderDetails') }}  #{{ $booking->id }}, {{ trans('admin.booking_statuses.' . $booking->status) }}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M9.68273 10.7435L1.19745 2.25823C0.907539 1.96831 0.907539 1.48748 1.19745 1.19757C1.48737 0.907653 1.9682 0.907653 2.25811 1.19757L10.7434 9.68285C11.0333 9.97276 11.0333 10.4536 10.7434 10.7435C10.4535 11.0334 9.97265 11.0334 9.68273 10.7435Z" fill="#292D32"/>
							<path d="M1.19752 10.7435C0.907609 10.4536 0.907609 9.97276 1.19752 9.68285L9.6828 1.19757C9.97272 0.907653 10.4536 0.907653 10.7435 1.19757C11.0334 1.48748 11.0334 1.96831 10.7435 2.25823L2.25818 10.7435C1.96827 11.0334 1.48744 11.0334 1.19752 10.7435Z" fill="#292D32"/>
							</svg>
						</button>
					  </div>
					  <div class="AssignPopupContentWrap">
						<div class="AssignPopupContentWrapInner">
							<h6>Цена</h6>
							<ul class="heading_list">
								<li><span>{{ trans('admin.initialPrice') }} <b>{{ number_format($booking->delivery_price, 0, " ", " ") }}</b></span></li>
								{{-- <li><span>{{ trans('admin.additionalPrice') }} <b>{{ number_format(($booking->pickup_price+$booking->unloading_price+$booking->round_pickup_price+$booking->round_unloading_price+$booking->additional_price),0," "," ") }} сум</b></span></li> --}}
								<li><span class="total">{{ trans('admin.totalPrice') }} <b>{{ number_format($booking->price, 0, " ", " ") }} <sub> {{ trans('admin.sum') }}</sub></b></span></li>
							</ul>
							{{-- <ul class="heading_list" style="margin-top:20px; display:block;">
								<li><span>{{ trans('admin.additionalPrice') }}: <b>{{ $booking->additional_price }}</b></span></li>
								<li><span>Комментарий: <b>{{ $booking->additional_price_review ?? 'Нет' }}</b></span></li>
							</ul> --}}



							<div class="w-100 float-left loadingTableWrapper">
								  <ul>
									  <li><span><b>{{ trans('admin.loading') }}:</b> {{ number_format($booking['carType']->pickup_per_minute, 0, " ", " ") }} {{ trans('admin.sum') }} / {{ trans('admin.minute') }}</span></li>
									  <li><span><b>{{ trans('admin.tariffs') }}</b></span></li>
									  <li><span><b>{{ trans('admin.unloading') }}:</b> {{ number_format($booking['carType']->unloading_per_minute, 0, " ", " ") }} {{ trans('admin.sum') }} / {{ trans('admin.minute') }}</span></li>
								  </ul>
								  <div class="w-100 float-left pointColumnWrap">

									  <div class="pointColumn">
										  <div class="HeadingboxColumn">
											  <div class="point">{{ trans('admin.point') }} <span>A</span> - <span>B</span></div>
										  </div>

										  <div class="loadingProcessRowWrap">
											  <div class="loadingProcess">
												  <div class="loadingProcessContent">
													  <span class="status">{{ trans('admin.loadingMinutes') }}</span>
													  <span class="statusTime">{{$booking->pickup_overtime}}</span>
												  </div>
											  </div>
											  <div class="loadingProcess">
												  <div class="loadingProcessContent middle">
													  <span class="status ">{{($booking->pickup_overtime + $booking->unloading_overtime)}} {{ trans('admin.minutes') }}</span>
													  <span class="statusTime">{{number_format((($booking->pickup_price + $booking->unloading_price)), 0, " ", " ")}} {{ trans('admin.sum') }}</span>
												  </div>
											  </div>
											  <div class="loadingProcess">
												  <div class="loadingProcessContent">
													  <span class="status">{{ trans('admin.unloadingMinutes') }} {{ trans('admin.minutes') }}</span>
													  <span class="statusTime">{{$booking->unloading_overtime}}</span>
												  </div>
											  </div>
										  </div>

									  </div>

								  </div>
								  @if($booking->is_round_trip == 1)
									  <div class="w-100 float-left pointColumnWrap">

									  <div class="pointColumn">
										  <div class="HeadingboxColumn">
											  <div class="point">{{ trans('admin.point') }} <span>B</span> - <span>A</span></div>
										  </div>

										  <div class="loadingProcessRowWrap">
											  <div class="loadingProcess">
												  <div class="loadingProcessContent">
													  <span class="status">{{ trans('admin.loadingMinutes') }}</span>
													  <span class="statusTime">{{$booking->round_pickup_overtime}}</span>
												  </div>
											  </div>
											  <div class="loadingProcess">
												  <div class="loadingProcessContent middle">
													  <span class="status ">{{($booking->round_pickup_overtime + $booking->round_unloading_overtime)}} {{ trans('admin.minutes') }}</span>
													  <span class="statusTime">{{number_format((($booking->round_pickup_price + $booking->round_unloading_price)), 0, " ", " ")}} {{ trans('admin.sum') }}</span>
												  </div>
											  </div>
											  <div class="loadingProcess">
												  <div class="loadingProcessContent">
													  <span class="status">{{ trans('admin.unloadingMinutes') }}</span>
													  <span class="statusTime">{{$booking->round_unloading_overtime}}</span>
												  </div>
											  </div>
										  </div>

									  </div>

								  </div>
								  @endif
							</div>
						</div>
					  </div>

					</div>
				  </div>
				</div>

						<div id="map" class="w-100 float-left"></div>
					@endcomponent

					<div class="modal fade" id="CompanyModal"  aria-labelledby="CompanyModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="CompanyModalLabel">Назначить водителя</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<form action="{{ route('admin.bookings.change-state', $booking) }}" method="post">
										<input type="hidden" name="status" value="accepted">
										<div class="row">
											<div class="col-12">
												<div class="form-group">
													<label for="driver_id">Выберите водителя</label>
													<select name="driver_id" id="driver_id" class="form-control js-select d-block" required>
														@foreach($drivers as $driver)
															@if($driver->user)
																<option value="{{$driver->user->id}}">
																	{{$driver->user->id}} - {{ ($driver->user->profile->surname ?? '') . ' ' . ($driver->user->profile->name ?? '') . ' ' . ($driver->user->profile->middle_name ?? '') }}
																</option>
															@endif
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-12 text-center">
												<button class="btn btn-primary">Назначить</button>
											</div>
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
								</div>
							</div>
						</div>
					</div>

					<div class="modal fade" id="changeStateModal"  aria-labelledby="changeStateModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="changeStateModalLabel">Изменить статус заказа</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<form action="{{ route('admin.bookings.change-status', $booking) }}" method="post">
										<div class="row">
											<div class="col-12">
												<div class="form-group">
													<label for="status">Выберите статус</label>
													<select name="status" id="status" class="form-control js-select d-block" required>
														@foreach(TruckBooking::availableStatuses() as $status)
																<option value="{{ $status }}">
																	@lang('admin.booking_statuses.' . $status)
																</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="col-12 text-center">
												<button class="btn btn-primary">Изменить</button>
											</div>
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
								</div>
							</div>
						</div>
					</div>

					<div class="modal fade" id="cancelModal"  aria-labelledby="cancelModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="cancelModalLabel">Отменить заказ</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<form action="{{ route('admin.bookings.cancel', $booking) }}" method="post">
										<div class="row">
											<div class="col-12">
												<div class="form-group">
													<label for="cancel_reason_id">Выберите причину</label>
													<select name="cancel_reason_id" id="cancel_reason_id" class="form-control d-block" required>
														<option value=""></option>
														@foreach(\App\Domain\CancelReasons\Models\CancelReason::all() as $reason)
																<option value="{{$reason->id}}">
																	{{ $reason->reason }}
																</option>
														@endforeach
														<option value="null">Другое</option>
													</select>
												</div>
											</div>

											<div class="col-12">
												<div class="form-group">
													<label for="cancel_reason_comment">Дополнительный комментарий</label>
													<textarea class="form-control" name="cancel_reason_comment" id="cancel_reason_comment"  rows="5" ></textarea>
												</div>
											</div>
											<div class="col-12 text-center">
												<button type="submit" class="btn btn-danger" onclick="
													if (!confirm('@lang('admin.destroy_confirm')')) {
													return false;
													}
													">Отменить заказ</button>
											</div>
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
								</div>
							</div>
						</div>
					</div>
					@push('scripts')
						<script type="text/javascript" src="{{ asset('vendor/inputmask/jquery.inputmask.bundle.js') }}"></script>
						<script>
						$(function () {
							$('.price-input').inputmask({
								alias: 'numeric',
								placeholder: '0',
								groupSeparator: ' ',
								autoGroup: true,
								digits: 2,
								digitsOptional: true
							});
						});

						$(function () {
							$('.edit-price-input').inputmask({
								alias: 'numeric',
								placeholder: '0',
								groupSeparator: ' ',
								autoGroup: true,
								digits: 2,
								digitsOptional: true
							});
						});
						</script>

					@endpush

					<div class="modal fade" id="additionalChargeModal"  aria-labelledby="additionalChargeModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="additionalChargeModalLabel">Изменить цену (клиент)</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<form action="{{ route('admin.bookings.editPrice', $booking) }}" method="post">
										<div class="row">
											<div class="col-10">
												<div class="form-group">
													<label for="client_price">Введите сумму</label>
													<input class="form-control price-input" type="text" maxLength='14' name="booking_price" id="client_prices" value="{{ $booking->price ?? 0 }}" required>
												</div>
											</div>
											<div class="col-2">
												<div style='padding: 40px 5px'>
													сум
												</div>
											</div>

											<div class="col-12 text-center">
												<button type="submit" class="btn btn-warning">Изменить сумму</button>
											</div>
										</div>

										<input type="hidden" name="type" value="client_price">
									</form>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
								</div>
							</div>
						</div>
					</div>

					<div class="modal fade" id="changePriceModal"  aria-labelledby="changePriceModal" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="changePriceModalLabel">Изменить цену (водитель)</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<form action="{{ route('admin.bookings.editPrice', $booking) }}" method="post">
										<div class="row">
											<div class="col-10">
												<div class="form-group">
													<label for="booking_price">Введите сумму</label>
													<input class="form-control edit-price-input" type="text" maxLength='14' name="booking_price" id="booking_price" value="{{ $booking->driver_price ?? 0 }}" required>
												</div>
											</div>
											<div class="col-2">
												<div style='padding: 40px 5px'>
													сум
												</div>
											</div>

											<div class="col-12 text-center">
												<button type="submit" class="btn btn-warning">Изменить сумму</button>
											</div>
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
								</div>
							</div>
						</div>
					</div>

@endsection

@push('styles')
    <style>
        #map {
            width: 100%;
            height: 80vh;
        }
    </style>
@endpush

@push('scripts')
    <script defer src="https://api-maps.yandex.ru/2.1/?apikey={{env("YMAPS_API_KEY")}}&lang=ru_RU" type="text/javascript"></script>
    <script>
        $(function () {
            ymaps.ready(init);

            function init() {
                var myMap = new ymaps.Map("map", {
                    center: [41.326681, 69.244031],
                    zoom: 17
                }, {
                    searchControlProvider: 'yandex#search'
                });
                @php
$routes = json_decode($booking->routes, true);
                @endphp

                @if(isset($routes[0]['lat']) && isset($routes[0]['lng']) && isset($routes[1]['lat']) && isset($routes[1]['lng']))
                var multiRoute = new ymaps.multiRouter.MultiRoute({
                    // Описание опорных точек мультимаршрута.
                    referencePoints: [
                        @foreach($routes as $route)
							[{{ $route['lat'] }}, {{$route['lng']}}],
                        @endforeach
                    ],
                    // Параметры маршрутизации.
                    params: {
                        // Ограничение на максимальное количество маршрутов, возвращаемое маршрутизатором.
                        results: 1
                    }
                }, {
                    // Автоматически устанавливать границы карты так, чтобы маршрут был виден целиком.
                    boundsAutoApply: true
                });

				@foreach($drivers_nearby as $car)
				@if($car->user && $car->user->profile && $car->user->profile->lat && $car->user->profile->lng)
				myMap.geoObjects.add(
						new ymaps.Placemark([{{$car->user->profile->lat}}, {{$car->user->profile->lng}}], {
							hintContent: '{{$car->user->car->number ?? '--'}}',
							balloonContent: '{{$car->user->car->number ?? '--'}} <br>{{ (($car->user->profile->surname ?? '') . ' ' . ($car->user->profile->name ?? '') . ' ' . ($car->user->profile->middle_name ?? '')) }} <br>+{{$car->user->username ?? '--'}} <br>Последнее действие: {{$car->user->profile->updated_at->format("d M Y H:i") ?? '--'}}'
						}, {
							iconLayout: 'default#image',
							iconImageSize: [20, 20],
							iconImageHref: '{{ ($car->user->last_seen_at && (now()->timestamp - $car->user->last_seen_at->timestamp < (24 * 3600))) ? asset('images/green-circle.png') : asset('images/red-circle.png')}}'
						})
				);
				@endif
				@endforeach



                myMap.geoObjects.add(multiRoute);
                @endif

                @if($booking->driver_id && isset($booking->driver->user) && isset($booking->driver->lat) && isset($booking->driver->lng))
                myMap.geoObjects.add(
                    new ymaps.Placemark([{{$booking->driver->lat}}, {{$booking->driver->lng}}], {
                        hintContent: '{{$booking->driver->user->car->number ?? '--'}}',
                        balloonContent: '{{$booking->driver->user->car->number ?? '--'}} <br>{{ (($booking->driver->surname ?? '') . ' ' . ($booking->driver->name ?? '') . ' ' . ($booking->driver->middle_name ?? '')) }} <br>+{{$booking->driver->user->username ?? '--'}}'
                    }, {
                        iconLayout: 'default#image',
                        iconImageSize: [20, 20],
                        iconImageHref: '{{ asset('images/green-circle.png') }}'
                    })
                );
                @endif

                @if(false)

                @endif
            }
        });
    </script>
@endpush
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            display: block;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {

            $('#CompanyModal').on('show.bs.modal', function (event) {
                $('.js-select').select2();
            })

        });
		
		/* $(".inputPassword i.eyeIcon").click(function() {
			$(this).toggleClass("eye-slash");
			input = $(this).parent().find("input");
			if (input.attr("type") == "password") {
				input.attr("type", "text");
			} else {
				input.attr("type", "password");
			}
		}); */
    </script>
@endpush
