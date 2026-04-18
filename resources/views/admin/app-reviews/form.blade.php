@extends('admin.layout')
 @section('center_content')
    @component('component.card', ['title' => 'Добавить отзыв', 'bodyClass' => 'card-body-no-padding'])
    <form action="{{ route('admin.frontend.app-reviews.add', $review) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group row {!! $errors->first('company', 'has-danger')!!}">
            <label class="col-md-3 text-md-right col-form-label-sm" for="company">Наименование компании</label>
            <div class="col-md-4">
                <input type="text" name="company" class="form-control input-sm" id="company" value="{{ old('company', $review->company ?? '') }}" required autofocus>
                {!! $errors->first('company', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
        
        <div class="form-group row {!! $errors->first('director', 'has-danger')!!}">
            <label class="col-md-3 text-md-right col-form-label-sm" for="director">Имя руководителя</label>
            <div class="col-md-4">
                <input type="text" name="director" class="form-control input-sm" id="director" value="{{ old('director', $review->director ?? '') }}" required>
                {!! $errors->first('director', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
        
        <div class="form-group row {!! $errors->first('director_role', 'has-danger')!!}">
            <label class="col-md-3 text-md-right col-form-label-sm" for="director_role">Должность</label>
            <div class="col-md-4">
                <input type="text" name="director_role" class="form-control input-sm" id="director_role" value="{{ old('director_role', $review->director_role ?? '') }}" required>
                {!! $errors->first('director', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
        
        <div class="form-group row {!! $errors->first('logo', 'has-danger')!!}">
            <label class="col-md-3 text-md-right col-form-label-sm" for="logo">Логотип</label>
            <div class="col-md-4">
                <input type="file" accept='.jpg,.jpeg,.png,.svg' name="logo" id="logo" />
                {!! $errors->first('logo', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
        
        <div class="form-group row {!! $errors->first('review', 'has-danger')!!}">
            <label class="col-md-3 text-md-right col-form-label-sm" for="review">Отзыв</label>
            <div class="col-md-4">
                <textarea rows="10" name="review" class="form-control input-sm" id="review" required>{{ old('review', $review->review ?? '') }}</textarea>
                {!! $errors->first('director', '<small class="form-control-feedback">:message</small>') !!}
            </div>
        </div>
        
        <div class="form-group row">
            <label class="col-md-3 text-md-right col-form-label-sm" for="published">Опубликован</label>
            <div class="col-md-4">
                <div class="btn-group btn-group-sm" data-toggle="buttons">
                    <label class="btn btn-outline-success {{ old('published', $review->published ?? 0) == 1 ? 'active': '' }}">
                        <input type="radio" name="published" value="1" {{ old('published', $review->published ?? 0) == 1? 'checked': '' }} required>
                        Да
                    </label>
                    <label class="btn btn-outline-danger {{ old('published', $reviev->published ?? 0) == 0 ? 'active': '' }}">
                        <input type="radio" name="published" value="0" {{ old('published', $review->published ?? 0) == 0? 'checked': '' }} required>
                        Нет
                    </label>
                </div>
            </div>
        </div>
        <div class="px-5 py-3">
            <a href="{{ route('admin.frontend.app-reviews') }}" class="btn btn-sm btn-danger float-left">@lang('admin.back')</a>
            <button class="btn btn-sm btn-success float-right px-3 ">@lang('admin.create')</button>
            <div class="clearfix"></div>
        </div>
    </form>


    @endcomponent
@endsection
