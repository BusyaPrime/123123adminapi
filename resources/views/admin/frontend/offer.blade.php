@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Frontend', 'bodyClass' => 'card-body-no-padding'])
        <div class="container">
            <div class="row pb-5">
                <div class="col">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active pl-5 pr-5 mr-2" id="ru_tab-btn" data-toggle="tab"
                                data-target="#ru_tab" type="button" role="tab" aria-controls="ru_tab"
                                aria-selected="true">RU</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link pl-5 pr-5 mr-2" id="uz_tab-btn" data-toggle="tab" data-target="#uz_tab"
                                type="button" role="tab" aria-controls="uz_tab" aria-selected="false">UZ</button>
                        </li>
                        {{-- <li class="nav-item" role="presentation">
                            <button class="nav-link pl-5 pr-5 mr-2" id="en_tab-btn" data-toggle="tab" data-target="#en_tab"
                                type="button" role="tab" aria-controls="en_tab" aria-selected="false">EN</button>
                        </li> --}}
                    </ul>
                    <form action="{{ route('admin.frontend.store-offer') }}" method="POST" enctype="multipart/form-data">
                        <div class="tab-content pt-2 pb-2" id="myTabContent">

                            <div 
                                class="tab-pane fade show active tab-content"
                                id="ru_tab"
                                role="tabpanel"
                                data-lang="ru"
                                aria-labelledby="ru_tab-btn"
                            >
                                <label for="file-ru">Прикрепите файл RU</label><br />
                                <input type="file" id="file-ru" name="file-ru" accept="application/pdf"/>
                            </div>

                            <div class="tab-pane fade tab-content" id="uz_tab" role="tabpanel" data-lang="uz" aria-labelledby="uz_tab-btn">
                                <label for="file-uz">Прикрепите файл UZ</label><br />
                                <input type="file" id="file-uz" name="file-uz" accept="application/pdf"/>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Загрузить оферту</button>
                    </form>
                </div>
            </div>
        </div>

    @endcomponent
@endsection



@push('scripts')
    <script>
        
    </script>
@endpush
