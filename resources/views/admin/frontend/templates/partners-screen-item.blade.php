<div class="row mb-3 col align-items-center partners-block-item">
    <div class="col"></div>
    <div class="col-2">
        <div class="input-group">
            <div class="w-100 position-relative" style="height: 100px;">
                <label class='gallery-image-selector position-absolute' for="partners-scr-icon-selector{{ isset($index) ? "_$index": "" }}">
                    <div class="w-100 position-absolute d-flex align-items-center justify-content-center upload-icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <img src="{{ isset($item['icon']) && isset($item['icon']['filename']) && isset($item['icon']['path']) ? asset($item['icon']['path'].'/'.$item['icon']['filename']) : '' }}" class="w-100 gallery-img why-scr-icon-img" />
                    <p class="text-black gallery-img-choose">Иконка</p>
                </label>
                <input class="gallery-image-file d-none" name="settings[partners][{{ $index ?? 'index' }}][icon]" type="file" id='partners-scr-icon-selector{{ isset($index) ? "_$index": "" }}' accept=".jpg,.jpeg,.png,.gif,.mp4,.svg" />
                <input class="gallery-image-file-default" name="settings[partners][{{ $index ?? 'index' }}][default]" type="hidden" value="{{ isset($item['icon']) && isset($item['icon']['path']) && isset($item['icon']['filename']) ? json_encode(['filename' => $item['icon']['filename'], 'path' => $item['icon']['path']]) : '' }}" />
            </div>
        </div>
    </div>
    <div class="col-8">
        <div class="input-group d-flex align-items-center">
            <label for="partner-logo-desc{{ isset($index) ? "_$index": "" }}" class="m-0 pl-2 pr-2 gallery-img-icon-desc">Meta title: </label>
            <input type="text" id='partner-logo-desc{{ isset($index) ? "_$index": "" }}' class="form-control gallery-icon-desc-input" name="lang[partners][{{ $index ?? 'index' }}][imageAlt]" value="{{ isset($langData) ? $langData['imageAlt'] : '' }}" />
        </div>
    </div>
    <div class="col">
        <div class="input-group d-flex align-items-center">
            <button class="btn btn-danger partners-scr-icon-delete" type="button">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</div>