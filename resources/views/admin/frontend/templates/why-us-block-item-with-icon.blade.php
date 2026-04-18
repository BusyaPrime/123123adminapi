<div class="row mb-3 col align-items-center why-us-block-item-with-icon">
    <div class="col"></div>
    <div class="col-2">
        <div class="input-group">
            <div class="w-100 position-relative" style="height: 100px;">
                <label class='gallery-image-selector position-absolute' for="why-scr-icon-selector{{isset($index) && isset($mainIndex) ? "_$mainIndex\_$index": ''}}">
                    <div class="w-100 position-absolute d-flex align-items-center justify-content-center upload-icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <img src="{{ isset($data) && isset($data['icon']) && isset($data['icon']['path']) && isset($data['icon']['filename']) ? asset($data['icon']['path'].'/'.$data['icon']['filename']) : '' }}" class="w-100 gallery-img why-scr-icon-img" />
                    <p class="text-black gallery-img-choose">Иконка</p>
                </label>
                <input class="gallery-image-file d-none" type="file"
                    accept=".jpeg,.jpg,.png,.gif,.mp4,.svg"
                    id='why-scr-icon-selector{{isset($index) && isset($mainIndex) ? "_$mainIndex\_$index": ''}}' name="settings[contents][{{ $mainIndex??'mainIndex' }}][icons][{{ $index??'index' }}][icon]" />
                <input type="hidden" class="img-subitem-input-hidden" name="settings[contents][{{ $mainIndex??'mainIndex' }}][icons][{{ $index??'index' }}][default]" value="{{isset($data['icon']['filename']) && isset($data['icon']['path']) && isset($data['icon']) ? json_encode(['filename' => $data['icon']['filename'], 'path' => $data['icon']['path']]) : ''}}" />
            </div>
        </div>
    </div>
    <div class="col-8">
        <div class="input-group d-flex align-items-center">
            <label for="gallery-image-icon-desc{{isset($index) && isset($mainIndex) ? "_$mainIndex\_$index": ''}}" class="m-0 pl-2 pr-2 gallery-img-icon-desc">Текст:
            </label>
            <input type="text" id='gallery-image-icon-desc{{isset($index) && isset($mainIndex) ? "_$mainIndex\_$index": ''}}' class="form-control gallery-icon-desc-input" value="{{ isset($langData) ? $langData['description'] : '' }}"
                name="lang[contents][{{ $mainIndex??'mainIndex' }}][icons][{{ $index??'index' }}][description]"
            />
        </div>
    </div>
    <div class="col">
        <div class="input-group d-flex align-items-center">
            <button class="btn btn-danger why-scr-icon-delete" type="button">
                <i class="fas fa-trash"></i>
            </button>

        </div>
    </div>
</div>