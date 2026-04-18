@extends('admin.layout')
 @section('center_content')
    @component('component.card', ['title' => 'Отзывы', 'bodyClass' => 'card-body-no-padding'])
        @slot('buttons')
            <div class="row col-sm-6 justify-content-end">
                <a href="{{ route('admin.frontend.app-reviews.form') }}" class="btn btn-success">
                    <span class="d-none d-sm-inline-block">Добавить</span>
                </a>
            </div>
        @endslot

        @if($reviews->isNotEmpty())
            <table style="border-spacing: 0;" class="table table-bordered table-hover">
                <thead>
                    <td>ID</td>
                    <td>Компания</td>
                    <td>Статус</td>
                    <td>Действия</td>
                </thead>
                    @foreach($reviews as $review)
                        <tr>
                            <td class="p-3">{{ $review->id }}</td>
                            <td class="p-3">{{ $review->company }}</td>
                            <td class="p-3 {{$review->published == 1 ? 'text-success' : 'text-danger'}}">{{ $review->published == 1 ? 'Опубликовано' : 'Не опубликовано' }}</td>
                            <td class="table__td">
                                <div class="dropdown">
                                    <a   class="dropdown-toggle dropdown-no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h fa-2x"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('admin.frontend.app-reviews.show', $review) }}" class="dropdown-item">
                                            Просмотр
                                        </a>
                                        <a href="{{ route('admin.frontend.app-reviews.form', $review) }}" class="dropdown-item">
                                            @lang('admin.edit')
                                        </a>
                                        <div class="dropdown-item " onclick="
                                            if (confirm('@lang('admin.destroy_confirm')')) {
                                            $(this).find('form').submit();
                                            }
                                            ">
                                            <form action="{{ route('admin.frontend.app-reviews.delete', $review) }}" id="delete_form" class="d-inline-block" method="post">
                                                @csrf
                                                @method('delete')
                                                <span class="d-block text-danger">@lang('admin.delete')</span>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
            </table>
        @endif

        @slot('bottom')
            @include('ui.pagination', ['data' => $reviews])
        @endslot
    @endcomponent
@endsection
