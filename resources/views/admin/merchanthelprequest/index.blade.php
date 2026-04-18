@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Запросы на подключение', 'bodyClass' => 'card-body-no-padding'])
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
@endslot
        @if($merchanthelprequest->isNotEmpty())
            <div class="table-responsive">
                <table class="table">
                    <thead class="table__thead">
                        <tr>
                            <th class="table__th">@lang('admin.id')</th>
                            <th class="table__th">Телефон</th>
                            <th class="table__th">Дата регистрации</th>
                            <th class="table__th">Статус</th>
							<th class="table__th"></th>
                        </tr>
                    </thead>
                    <tbody class="table__tbody">
                    @foreach($merchanthelprequest as $req)
                        <tr class="table__tr mt-2 mb-2 "  >
                            <td class="table__td">{{ $req->id }}</td>
                            <td class="table__td">+998 {{ substr($req->username, 3, 2) }} {{ substr($req->username, 5, 3) }} {{ substr($req->username, 8, 2) }} {{ substr($req->username, 10, 2) }}</td>
                            <td class="table__td">{{ date('d F Y в h:i', strtotime($req->created_at))  }}</td>
                            <td class="table__td"><button class="btn {{ $req->is_active == 1 ? 'btn-warning' : 'btn-success' }}">{{ $req->is_active === 1 ? 'Не закрыт' : 'Закрыт' }}</button></td>
							
                            <td class="table__td">
                                <div class="dropdown"  >
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
										<div class="dropdown-item " onclick="
                                            if (confirm('Вы завершили этот запрос на подключение ?')) {
                                            	$(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.merchanthelprequests.edit', [$req, 'page' => request()->get('page')]) }}" id="update_form" class="d-inline-block" method="post">
                                                @csrf
												<input type="hidden" name="id" value="{{ $req->id }}">
                                                <span class="d-block text-success"
                                                      >
													  Завершено
                                                </span>
                                            </form>
                                        </div>
										<div class="dropdown-item " onclick="
                                            if (confirm('Вы действительно хотите удалить этот запрос ?')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.merchanthelprequests.delete', [$req, 'page' => request()->get('page')]) }}" id="delete_form" class="d-inline-block" method="post">
                                                @csrf
												<input type="hidden" name="id" value="{{ $req->id }}">
												@method('delete')
                                                <span class="d-block text-danger"
                                                      >
													  Удалить
                                                </span>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @slot('bottom')
		
		@include('ui.pagination', ['data' => $merchanthelprequest])
        @endslot
    @endcomponent
	<script>
	/* window.onload = function() {
	$(".checkbox").change(function() {
		var Current = "";
		var Android = "";
		var iOS = "";
		var Customer = "";
		var Driver = "";
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
							row +='<td class="table__td"><span class="badge badge-success">{{trans("admin.active")}}</span></td>';
						}
						else
						{
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
		
	});
	}; */
	</script>
@endsection