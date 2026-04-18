@extends('admin.layout')

@section('center_content')
    <form action="{{ route('admin.appversions.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @component('component.card', ['title' => "Add App Version", 'title_class' => 'col-sm-12'])
            @include('admin.appversions._form')
            @slot('bottom')
                <div class="px-5 py-3">
                <a href="{{ route('admin.appversions.index') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
                <button class="btn btn-sm btn-primary float-right">@lang('admin.create')</button>
                <div class="clearfix"></div>
                </div>
            @endslot
        @endcomponent
    </form>
@endsection