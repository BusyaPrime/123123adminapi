<div class="row d-flex align-items-center pb-4 gallery-photo-item">
    <div class="col-md-2 position-relative" style="height: 100px; overflow:hidden;">
        <label class='gallery-image-selector position-absolute' for="third-screen-gallery-image{{ isset($lang) && isset($index) ? "_$lang.$index" : '' }}">
            <div class="w-100 position-absolute d-flex align-items-center justify-content-center upload-icon">
                <i class="fas fa-upload"></i>
            </div>
            <img src={{ isset($image) && isset($image['filename']) && isset($image['path']) ? asset($image['path'].'/'.$image['filename']) : '' }} alt="Выберите изображение" title="Изображение" class="w-100 gallery-img" />
        </label>
        <input class="gallery-image-file d-none" type="file" id='third-screen-gallery-image{{ isset($lang) && isset($index) ? "_$lang.$index" : '' }}' name="settings[gallery][{{$index??''}}][image]" accept=".jpg,.jpeg,.png,.gif,.mp4" />
        <input type="hidden" class="default-image" value="{{ isset($image) && isset($image['filename']) ? json_encode(['filename' => $image['filename'], 'path' => $image['path']]) : '' }}" name="settings[gallery][{{$index??''}}][default]" />
        {{-- <input type="text" name="settings[gallery][][image]" id=""> --}}
    </div>
    <div class="col-md-4">
        <div class="input-group d-flex align-items-center">
            <label for="gallery-image-title" class="m-0 pl-2 pr-2 gallery-img-desc-selector">Описание: </label>
            <input type="text" id='gallery-image-title' class="form-control gallery-img-desc" value="{{ isset($langData) ? $langData['description']??'' : '' }}" name="lang[gallery][{{$index??''}}][description]" />
        </div>
    </div>
    <div class="col-md-5">
        <div class="input-group d-flex align-items-center">
            <label for="gallery-image-alt1" class="m-0 pl-2 pr-2 gallery-img-alt-selector">Текст alt: </label>
            <input type="text" id='gallery-image-alt' class="form-control gallery-img-alt" value="{{ isset($langData) ? $langData['imageAlt']??'' : '' }}" name="lang[gallery][{{$index??''}}][imageAlt]" />
        </div>
    </div>
    <div class="col-md">
        <button class="btn btn-danger delete-image" type="button">
            <i class="fas fa-trash"></i>
        </button>
    </div>
</div>
