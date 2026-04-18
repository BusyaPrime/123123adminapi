@extends('admin.layout')
 @section('center_content')
    @component('component.card', ['title' => 'Просмотр - '.$review->company, 'bodyClass' => 'card-body-no-padding'])
        <div class="row">
            @if($review->logo)
                <div class="col-3">
                    <img src="{{ $review->imageUrl('logo') }}" />
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-3">
                <span>Наименование компании:</span>
            </div>
            <div class="col">
                <span>{{ $review->company }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <span>ФИО руководителя:</span>
            </div>
            <div class="col">
                <span>{{ $review->director }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <span>Должность:</span>
            </div>
            <div class="col">
                <span>{{ $review->director_role }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <span>Отзыв:</span>
            </div>
            <div class="col">
                <span>{{ $review->review }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <span>Статус:</span>
            </div>
            <div class="col">
                <span>{{ $review->published == 1 ? 'Опубликовано' : 'Не опубликовано' }}</span>
            </div>
        </div>
    @endcomponent
@endsection
