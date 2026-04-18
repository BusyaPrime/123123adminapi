@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Version Per Appliction', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')
<style>
.checkboxFullwidthRow {
    width: 100%;
    float: left;
    display: flex;
    align-items: center;
	padding:0 9px;
}
.checkboxFullwidthRow span.title {
    width: auto;
    float: left;
    font-weight: 600;
    padding-right: 14px;
    padding-left: 24px;
}

.checkboxFullwidthRow span.title.first {
    padding-left: 0;
}

.checkboxFullwidthRow input.checkbox {
    width: auto;
    float: left;
    margin: 0 6px 0 15px;
}
</style>

<div class="row col-sm-6 justify-content-end">

    
    <a href="{{ route('admin.appversions.create') }}" class="btn btn-info ml-2 px-3">
        <span class="d-none d-sm-inline-block">Добавить версию</span> <i class="icmn-plus"><!-- --></i>
    </a>
</div>
        @endslot
		{{--<input type="checkbox" class="checkbox" name="Android" value="android" onchange="FilterData('Android')"> Android
		<input type="checkbox" class="checkbox" name="iOS" value="ios" onchange="FilterData('iOS')"> iOS
		<input type="checkbox" class="checkbox" name="Customer" value="customer" onchange="FilterData('Customer')"> Customer
		<input type="checkbox" class="checkbox" name="Driver" value="driver" onchange="FilterData('Driver')"> Driver--}}
		<div class="checkboxFullwidthRow">
		<span class="title first">Platform</span>
		<input type="checkbox" class="checkbox ml-0" name="Android" id="Android" value="android"> Android
		<input type="checkbox" class="checkbox" name="iOS" id="iOS" value="ios"> iOS
		<span class="title second">Application Type</span>
		<input type="checkbox" class="checkbox ml-0" name="Customer" id="Customer" value="customer"> Клиент
		<input type="checkbox" class="checkbox" name="Driver" id="Driver" value="driver"> Водитель
		</div>
        @if($appversions->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">@lang('admin.id')</th>
                            <th class="table__th">@lang('validation.attributes.VersionNumber')</th>
                            <th class="table__th">Тип платформы</th>
                            <th class="table__th">Тип приложении</th>
                            <th class="table__th">@lang('validation.attributes.Status')</th>
                            <th class="table__th">Дата добавления</th>
                            <th class="table__th"></th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($appversions as $company)
						@if($company->is_active == 1)
                        <tr class="table__tr mt-2 mb-2 "  >
                            <td class="table__td">{{ $company->id }}</td>
                            <td class="table__td">{{ $company->version_no }}</td>
                            <td class="table__td">
								<?php if($company->app_type == 'android')
										{
											$AppType = "Android";
										}
										else
										{
											$AppType = "iOS";
										}
									?>
                                {{ $AppType }}
                            </td>
							<td class="table__td">
								<?php if($company->userrole == 'customer')
										{
											$UserRole = "Customer";
										}
										elseif($company->userrole == '')
										{
											$UserRole = "";
										}
										else
										{
											$UserRole = "Driver";
										}
									?>
                                {{ $UserRole }}
                            </td>
							<td class="table__td">
                                {!! $company->status_id? '<span class="badge badge-success">'.trans('admin.active').'</span>': '<span class="badge badge-danger">'.trans('admin.not_active').'</span>' !!}
                            </td>
                            <td class="table__td">
								<?php $timestamp = strtotime($company->depricated_date); $new_date = date('d.m.Y', $timestamp); if($company->depricated_date == "" || $company->depricated_date == NULL){ $new_date = ""; } ?>
                                {{ $new_date }}
                            </td>
                            <td class="table__td">
                                <div class="dropdown"  >
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <!--<a href="{{ route('admin.appversions.edit', $company) }}" class="dropdown-item">
                                            @lang('admin.deprecate')
                                        </a>-->
										<div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.update_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.appversions.update', $company) }}" id="update_form" class="d-inline-block" method="post">
                                                @csrf
												<input type="hidden" name="id" value="{{ $company->id }}">
                                                <span class="d-block"
                                                      >
                                                    @lang('admin.deprecate')
                                                </span>
                                            </form>
                                        </div>
                                        <div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.destroy_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.appversions.destroy', $company) }}" id="delete_form" class="d-inline-block" method="post">
                                                @csrf
                                                @method('delete')
												<input type="hidden" name="removeid" value="{{ $company->id }}">
                                                <span class="d-block text-danger"
                                                      >
                                                    @lang('admin.delete')
                                                </span>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
						@endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @slot('bottom')
		
		@include('ui.pagination', ['data' => $appversions])
        @endslot
    @endcomponent
	<script>
	window.onload = function() {
	$(".checkbox").change(function() {
		var Current = "";
		var Android = "";
		var iOS = "";
		var Customer = "";
		var Driver = "";
		/* if(this.checked) {
			//Do stuff
			Current = this.value;
			if(Current == "android")
			{
				Android = "android";
			}
			if(Current == "ios")
			{
				iOS = "ios";
			}
			if(Current == "customer")
			{
				Customer = "customer";
			}
			if(Current == "driver")
			{
				Driver = "driver";
			}
		} */
		if($('#Android').is(":checked"))
		{
			Android = "android";
		}
		if($('#iOS').is(":checked"))
		{
			iOS = "ios";
		}
		if($('#Customer').is(":checked"))
		{
			Customer = "customer";
		}
		if($('#Driver').is(":checked"))
		{
			Driver = "driver";
		}
		var row = "";
		$.ajax({
			/* async: false, */
			url: "{{ route('admin.appversions.filterdata') }}",
			cache: false,
			method: 'post',
			dataType:'json',
			data: {"_token": "{{ csrf_token() }}",Android:Android,iOS:iOS,Customer:Customer,Driver:Driver},
		}).done(function(result){
			console.log('Filter Data');
			console.log(result);
			if(result.length > 0)
			{
				var updateTranslation = "'"+'{{trans("admin.update_confirm")}}'+"'";
				var deleteTranslation = "'"+'{{trans("admin.destroy_confirm")}}'+"'";
				result.forEach(function callback(value, index) {
					if(value.is_active == 1)
					{
						if(value.app_type == "android")
						{
							var apptype = "Android";
						}
						else
						{
							var apptype = "iOS";
						}
						if(value.userrole == "customer")
						{
							var userrole = "Customer";
						}
						else
						{
							var userrole = "Driver";
						}
						row +='<tr class="table__tr mt-2 mb-2 "  ><td class="table__td">'+value.id+'</td><td class="table__td">'+value.version_no+'</td><td class="table__td">'+apptype+'</td><td class="table__td">'+userrole+'</td>';
						if(value.status_id == 1)
						{
							/* alert('Success'+value.status_id); */
							row +='<td class="table__td"><span class="badge badge-success">{{trans("admin.active")}}</span></td>';
						}
						else
						{
							/* alert('Danger'+value.status_id); */
							row +='<td class="table__td"><span class="badge badge-danger">{{trans("admin.not_active")}}</span></td>';
						}
						if(value.depricated_date)
						{
							var today = new Date(value.depricated_date);
							var parts = value.depricated_date.split('-');
							var finaldate = parts[2].substring(0, 2)+'.'+parts[1]+'.'+parts[0];
						}
						else
						{
							var finaldate = "";
						}
						row +='<td class="table__td">'+finaldate+'</td><td class="table__td"><div class="dropdown"  ><a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h fa-2x"></i></a><div class="dropdown-menu dropdown-menu-right"><div class="dropdown-item " onclick="if(confirm('+updateTranslation+')) { $(this).find(\'form\').submit(); }"><form action="{{ route("admin.appversions.update") }}" id="update_form" class="d-inline-block" method="post"> @csrf <input type="hidden" name="id" value="'+value.id+'"><span class="d-block">{{trans("admin.deprecate")}}</span></form></div><div class="dropdown-item " onclick="if(confirm('+deleteTranslation+')) { $(this).find(\'form\').submit(); } "><form action="{{ route("admin.appversions.destroy") }}" id="delete_form" class="d-inline-block" method="post"> @csrf @method("delete") <input type="hidden" name="removeid" value="'+value.id+'"><span class="d-block text-danger"> {{trans("admin.delete")}} </span></form></div></div></div></td></tr>';
					}
				});
			}
			else
			{
				row +='<tr class="table__tr mt-2 mb-2 "  ><td colspan="6">No Data Found</td></tr>';
			}
			$('.table__tbody').html('');
			$('.table__tbody').html(row);
		})
		/* alert("Android=>"+Android);
		alert("iOS=>"+iOS);
		alert("Customer=>"+Customer);
		alert("Driver=>"+Driver); */
		
	});
	};
	</script>
@endsection