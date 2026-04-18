<div class="row pb-4 why-screen-content-item" data-order="{{$index??0}}">
    <div class="col-md-3 position-relative" style="height: 150px; overflow:hidden;">
        <label class='gallery-image-selector position-absolute' for="gallery-image{{ isset($index) ? "_$index": "" }}">
            <div class="w-100 position-absolute d-flex align-items-center justify-content-center upload-icon">
                <i class="fas fa-upload"></i>
            </div>
            <img src="{{isset($content) && isset($content['background']) && isset($content['background']['filename']) && isset($content['background']['path']) ?
            asset($content['background']['path'].'/'.$content['background']['filename']) : '' }}" class="w-100 gallery-img" />
            <p class="text-black gallery-img-choose">Выберите обложку</p>
        </label>
        <input class="gallery-image-file d-none" type="file" id='gallery-image{{ isset($index) ? "_$index": "" }}' name="settings[contents][{{ $index ?? '' }}][background]" accept=".jpeg,.jpg,.png,.gif,.mp4" />
        <input type="hidden" class="img-input-hidden" name="settings[contents][{{ $index ?? 'index' }}][default]" value="{{ isset($content['background']) && isset($content['background']['filename']) && isset($content['background']['path']) ? json_encode(['filename' => $content['background']['filename'], 'path' => $content['background']['path']]) : ''}}" />
    </div>
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-4">
                <div class="input-group d-flex align-items-center">
                    <label for="why-scr-image-title{{ isset($index) ? "_$index": "" }}" class="m-0 pl-2 pr-2 gallery-img-desc-selector">Описание:</label>
                    <input type="text" id='why-scr-image-title{{ isset($index) ? "_$index": "" }}' class="form-control gallery-img-desc" value="{{ isset($langData) && isset($langData['contents'][$index]['description']) ? $langData['contents'][$index]['description'] : '' }}" name="lang[contents][{{ $index ?? 'index' }}][description]"  />
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group d-flex align-items-center">
                    <label for="why-scr-gallery-image-alt{{ isset($index) ? "_$index": "" }}" class="m-0 pl-2 pr-2 gallery-img-alt-selector">Текст
                        alt: </label>
                    <input type="text" id='why-scr-gallery-image-alt{{ isset($index) ? "_$index": "" }}' class="form-control gallery-img-alt" value="{{ isset($langData) && isset($langData['contents'][$index]['imageAlt']) ? $langData['contents'][$index]['imageAlt'] : '' }}" name="lang[contents][{{ $index ?? 'index' }}][imageAlt]" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group d-flex align-items-center">
                    <label for="why-scr-color{{ isset($index) ? "_$index": "" }}" class="m-0 pl-2 pr-2" class="gallery-bg-color-label">Фон: </label>
                    <input type="color" class="form-control gallery-bg-color-input" id="why-scr-color{{ isset($index) ? "_$index": "" }}" value="{{ isset($content) && isset($content['bgColor']) ? $content['bgColor'] : '' }}" name="settings[contents][{{ $index ?? 'index' }}][bgColor]" />
                </div>
            </div>
            <div class="col-md">
                <button class="btn btn-danger why-scr-content-delete pl-3 pr-3" type="button">x</button>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">

                <div class="why-us-block-items-with-icon mb-2">
                    @if (isset($content) && isset($langData) && isset($content['icons']))
                        @foreach ($content['icons'] as $j => $data)
                            @component('admin.frontend.templates.why-us-block-item-with-icon', [
                                'data' => $data,
                                'langData' => isset($langData['contents'][$index]['icons'][$j]) ? $langData['contents'][$index]['icons'][$j] : null,
                                'mainIndex' => $index,
                                'index' => $j
                            ])@endcomponent
                        @endforeach
                    @endif
                </div>

                <hr />
                <button type="button" class="btn btn-success d-inline-block pl-3 pr-3 add-subitem-button">+ Добавить элемент</button>
            </div>
        </div>
    </div>
    <hr />
</div>
