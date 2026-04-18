<div class="tab-pane fade show {{ $lang == 'ru' ? 'active' : '' }} tab-content" id="{{ $lang }}_tab"
    role="tabpanel" data-lang="{{ $lang }}" aria-labelledby="{{ $lang }}_tab-btn">

    <div class="row pb-4">
        <div class="col">
            <form action="{{ route('admin.frontend.update') }}" class="site-settings" onsubmit="return false;"
                enctype="multipart/form-data">
                <div class="pb-4"></div>
                <div class="form-group pt-2 pb-1">
                    <label for="website-title">Заголовок</label>
                    <input type="text" id='website-title' name='lang[title]' class="form-control"
                        value="{{ $langData['metaInformations']['title'] }}">
                </div>
                <div class="form-group pt-1 pb-1">
                    <label for="website-meta">Meta description</label>
                    <input type="text" id='website-meta' name='lang[metaDescription]' class="form-control"
                        value="{{ $langData['metaInformations']['metaDescription'] }}" />
                    <input type="hidden" name="content-name" value="metaInformations">
                </div>
                <div class="form-group d-flex justify-content-end pt-1 pb-1">
                    <button type="submit" class="btn btn-success align-self-end save-settings">
                        <div class="text">Сохранить изменения</div>
                        <div class="loader pb-4 d-none" style="width: 20px; height: 20px;"><img class="w-100"
                                src="{{ asset('uploads/loader.gif') }}"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <hr />

    <div class="row pb-4">
        <div class="col">
            <form action="{{ route('admin.frontend.update') }}" method="post" onsubmit="return false;"
                class="site-settings">

                <div class="pb-4"></div>
                <h4>Первый экран</h4>
                <div class="form-group pt-2 pb-1">
                    <label for="first-screen-header">Заголовок</label>
                    <input type="text" id='first-screen-header' name="lang[header]" class="form-control"
                        value="{{ $langData['firstScreen']['header'] }}" />
                    <div class="pb-4"></div>
                    <div class="col-sm-6 p-0">
                        <div class="form-group d-inline pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="first-screen-heading1{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['firstScreen']['headerTag'] == 'h1' || $settings['firstScreen']['headerTag'] == '' ? 'checked' : '' }}
                                    value="h1">
                                <label for="first-screen-heading1{{ isset($lang) ? "_$lang" : '' }}">h1</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="first-screen-heading2{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['firstScreen']['headerTag'] == 'h2' ? 'checked' : '' }}
                                    value="h2">
                                <label for="first-screen-heading2{{ isset($lang) ? "_$lang" : '' }}">h2</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="first-screen-heading3{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['firstScreen']['headerTag'] == 'h3' ? 'checked' : '' }}
                                    value="h3">
                                <label for="first-screen-heading3{{ isset($lang) ? "_$lang" : '' }}">h3</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="first-screen-heading4{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['firstScreen']['headerTag'] == 'h4' ? 'checked' : '' }}
                                    value="h4">
                                <label for="first-screen-heading4{{ isset($lang) ? "_$lang" : '' }}">h4</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="first-screen-heading5{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['firstScreen']['headerTag'] == 'h5' ? 'checked' : '' }}
                                    value="h5">
                                <label for="first-screen-heading5{{ isset($lang) ? "_$lang" : '' }}">h5</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="first-screen-heading6{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['firstScreen']['headerTag'] == 'h6' ? 'checked' : '' }}
                                    value="h6">
                                <label for="first-screen-heading6{{ isset($lang) ? "_$lang" : '' }}">h6</label>
                            </div>
                        </div>
                    </div>
                    <div class="col pt-4">
                        <h4>Обложка</h4>
                        <div class="divider"></div>
                        <div class="pt-5 pb-2 text-center">
                            <div class="col-2">
                                <div class="input-group">
                                    <div class="w-100 position-relative" style="height: 100px;">
                                        <label
                                            class='gallery-image-selector position-absolute d-flex justify-content-center align-items-center'
                                            for="first-screen-background">
                                            <div
                                                class="w-100 position-absolute d-flex align-items-center justify-content-center upload-icon">
                                                <i class="fas fa-upload"></i>
                                            </div>
                                            <img src="{{ asset('uploads/frontend/firstScreen/' . $settings['firstScreen']['background']['filename']) }}"
                                                class="w-100 gallery-img why-scr-icon-img" />
                                            <p class="text-black gallery-img-choose">Обложка</p>
                                        </label>
                                        <input class="gallery-image-file d-none" name="settings[background]" type="file"
                                            accept=".png,.jpg,.jpeg,.gif,.mp4" id='first-screen-background' />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col d-flex justify-content-end">
                    <input type="hidden" name="content-name" value="firstScreen" />
                    <button type="submit" class="btn btn-success align-self-end save-settings">
                        <div class="text">Сохранить изменения</div>
                        <div class="loader pb-4 d-none" style="width: 20px; height: 20px;"><img class="w-100"
                                src="{{ asset('uploads/loader.gif') }}"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <hr />

    <div class="row pb-4">
        <div class="col">
            <form action="{{ route('admin.frontend.update') }}" method="post" onsubmit="return false;"
                class="site-settings">
                <div class="pb-4"></div>
                <h4>Экран калькулятора</h4>
                <div class="form-group pt-2 pb-1">
                    <label for="calc-screen-header">Заголовок</label>
                    <input type="text" id='calc-screen-header' name="lang[header]" class="form-control"
                        value="{{ $langData['calculator']['header'] }}" />
                    <div class="pb-4"></div>
                    <div class="col-sm-6 p-0">
                        <div class="form-group d-inline pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="calc-screen-heading1{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['calculator']['headerTag'] == 'h1' ? 'checked' : '' }}
                                    value="h1">
                                <label for="calc-screen-heading1{{ isset($lang) ? "_$lang" : '' }}">h1</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="calc-screen-heading2{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['calculator']['headerTag'] == 'h2' ? 'checked' : '' }}
                                    value="h2">
                                <label for="calc-screen-heading2{{ isset($lang) ? "_$lang" : '' }}">h2</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="calc-screen-heading3{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['calculator']['headerTag'] == 'h3' ? 'checked' : '' }}
                                    value="h3">
                                <label for="calc-screen-heading3{{ isset($lang) ? "_$lang" : '' }}">h3</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="calc-screen-heading4{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['calculator']['headerTag'] == 'h4' ? 'checked' : '' }}
                                    value="h4">
                                <label for="calc-screen-heading4{{ isset($lang) ? "_$lang" : '' }}">h4</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="calc-screen-heading5{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['calculator']['headerTag'] == 'h5' || $settings['calculator']['headerTag'] == '' ? 'checked' : '' }}
                                    value="h5">
                                <label for="calc-screen-heading5{{ isset($lang) ? "_$lang" : '' }}">h5</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="calc-screen-heading6{{ isset($lang) ? "_$lang" : '' }}"
                                    name="settings[headerTag]"
                                    {{ $settings['calculator']['headerTag'] == 'h6' ? 'checked' : '' }}
                                    value="h6">
                                <label for="calc-screen-heading6{{ isset($lang) ? "_$lang" : '' }}">h6</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col d-flex justify-content-end">
                    <input type="hidden" name="content-name" value="calculator" />
                    <button type="submit" class="btn btn-success align-self-end save-settings">
                        <div class="text">Сохранить изменения</div>
                        <div class="loader pb-4 d-none" style="width: 20px; height: 20px;"><img class="w-100"
                                src="{{ asset('uploads/loader.gif') }}"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <hr />

    <div class="row pb-4">
        <div class="col">
            <form action="{{ route('admin.frontend.update') }}" method="post" onsubmit="return false;"
                class="site-settings">
                <div class="pb-4"></div>
                <h4>Третий экран</h4>
                <div class="form-group pt-2 pb-1">
                    <label for="third-screen-header">Заголовок</label>
                    <input type="text" id='third-screen-header' name="lang[header]"
                        value="{{ $langData['thirdScreen']['header'] }}" class="form-control">
                    <div class="pb-4"></div>
                    <div class="col-sm-6 p-0">
                        <div class="form-group d-inline pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="third-screen-heading1{{ isset($lang) ? "_$lang" : '' }}"
                                    {{ $settings['thirdScreen']['headerTag'] == 'h1' ? 'checked' : '' }}
                                    name="settings[headerTag]" value="h1">
                                <label for="third-screen-heading1{{ isset($lang) ? "_$lang" : '' }}">h1</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="third-screen-heading2{{ isset($lang) ? "_$lang" : '' }}"
                                    {{ $settings['thirdScreen']['headerTag'] == 'h2' ? 'checked' : '' }}
                                    name="settings[headerTag]" value="h2">
                                <label for="third-screen-heading2{{ isset($lang) ? "_$lang" : '' }}">h2</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="third-screen-heading3{{ isset($lang) ? "_$lang" : '' }}"
                                    {{ $settings['thirdScreen']['headerTag'] == 'h3' ? 'checked' : '' }}
                                    name="settings[headerTag]" value="h3">
                                <label for="third-screen-heading3{{ isset($lang) ? "_$lang" : '' }}">h3</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="third-screen-heading4{{ isset($lang) ? "_$lang" : '' }}"
                                    {{ $settings['thirdScreen']['headerTag'] == 'h4' || $settings['thirdScreen']['headerTag'] == '' ? 'checked' : '' }}
                                    name="settings[headerTag]" value="h4">
                                <label for="third-screen-heading4{{ isset($lang) ? "_$lang" : '' }}">h4</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="third-screen-heading5{{ isset($lang) ? "_$lang" : '' }}"
                                    {{ $settings['thirdScreen']['headerTag'] == 'h5' ? 'checked' : '' }}
                                    name="settings[headerTag]" value="h5">
                                <label for="third-screen-heading5{{ isset($lang) ? "_$lang" : '' }}">h5</label>
                            </div>
                        </div>
                        <div class="form-group d-inline pl-2 pr-2">
                            <div class="icheck-primary d-inline">
                                <input type="radio" id="third-screen-heading6{{ isset($lang) ? "_$lang" : '' }}"
                                    {{ $settings['thirdScreen']['headerTag'] == 'h6' ? 'checked' : '' }}
                                    name="settings[headerTag]" value="h6">
                                <label for="third-screen-heading6{{ isset($lang) ? "_$lang" : '' }}">h6</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group pt-2 pb-1">
                    <label for="third-screen-content">Контент</label>
                    @component('component.tiny-mc')
                    @endcomponent
                    {{-- @push('scripts')
                        <script>
                            tinymce.init({
                                selector: 'textarea.third-screen-text'
                            });
                        </script>
                    @endpush --}}
                    <textarea type="text" name="lang[content]" id="third-screen-content" class="form-control third-screen-text text-editor">{{ $langData['thirdScreen']['content'] }}</textarea>
                </div>
                <div class="form-group pt-5 pb-1">
                    <label>Галерея</label>
                    <div class="pt-2 pb-0 text-center">
                        <div class="card card-default dropzone-third-screen-gallery">
                            <div class="card-body">
                                <div id='third-screen-gallery'>
                                    @foreach ($settings['thirdScreen']['gallery'] as $i => $gallery)
                                        @component('admin.frontend.templates.third-screen-item', [
                                            'image' => $gallery['image'],
                                            'langData' => isset($langData['thirdScreen']['gallery'][$i]) ? $langData['thirdScreen']['gallery'][$i] : null,
                                            'index' => $i,
                                            'lang' => $lang,
                                        ])
                                        @endcomponent
                                    @endforeach
                                </div>
                                <hr />
                                <button class="btn btn-success pl-3 pr-3 third-screen-gallery-add-item"
                                    type="button">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="col d-flex justify-content-end">
                        <input type="hidden" name="content-name" value="thirdScreen" />
                        <button type="submit" class="btn btn-success align-self-end save-settings">
                            <div class="text">Сохранить изменения</div>
                            <div class="loader pb-4 d-none" style="width: 20px; height: 20px;"><img class="w-100"
                                    src="{{ asset('uploads/loader.gif') }}"></div>
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <div class="row pb-4">
        <div class="col">
            <form action="{{ route('admin.frontend.update') }}" method="post" onsubmit="return false;" class="site-settings">
                <div class="pb-4"></div>
                <h4>Экран "Почему Casva ?"</h4>
                <div class="form-group pt-2 pb-1">
                    <label for="why-screen-header">Заголовок</label>
                    <input type="text" id='why-screen-header' name='lang[header]'
                        value="{{ $langData['whyUsScreen']['header'] }}" class="form-control">
                    <div class="pt-4"></div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group d-inline pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="why-screen-heading1{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['whyUsScreen']['headerTag'] == 'h1' ? 'checked' : '' }}
                                        value="h1">
                                    <label for="why-screen-heading1{{ isset($lang) ? "_$lang" : '' }}">h1</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="why-screen-heading2{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['whyUsScreen']['headerTag'] == 'h2' ? 'checked' : '' }}
                                        value="h2">
                                    <label for="why-screen-heading2{{ isset($lang) ? "_$lang" : '' }}">h2</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="why-screen-heading3{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['whyUsScreen']['headerTag'] == 'h3' ? 'checked' : '' }}
                                        value="h3">
                                    <label for="why-screen-heading3{{ isset($lang) ? "_$lang" : '' }}">h3</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="why-screen-heading4{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['whyUsScreen']['headerTag'] == 'h4' ? 'checked' : '' }}
                                        value="h4">
                                    <label for="why-screen-heading4{{ isset($lang) ? "_$lang" : '' }}">h4</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="why-screen-heading5{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['whyUsScreen']['headerTag'] == 'h5' ? 'checked' : '' }}
                                        value="h5">
                                    <label for="why-screen-heading5{{ isset($lang) ? "_$lang" : '' }}">h5</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="why-screen-heading6{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['whyUsScreen']['headerTag'] == 'h6' || $settings['whyUsScreen']['headerTag'] == '' ? 'checked' : '' }}
                                        value="h6">
                                    <label for="why-screen-heading6{{ isset($lang) ? "_$lang" : '' }}">h6</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group pt-2 pb-1">
                    <label for="why-screen-subtitle">Подзаголовок</label>
                    <input type="text" id='why-screen-subtitle' name='lang[description]' class="form-control" value="{{$langData['whyUsScreen']['description']}}" />
                </div>
                <div class="form-group pt-5 pb-1">
                    <h4 class="pb-2">Контент</h4>
                    <div class="pt-2 pb-0 text-center why-screen-content">
                        <div class="card card-default">
                            <div class="card-body">
                                <div id='why-screen-content' method="post" onsubmit="return false;">
                                    @foreach ($settings['whyUsScreen']['contents'] as $i => $item)
                                        @component('admin.frontend.templates.why-screen-item', [
                                            'content' => $item,
                                            'langData' => $langData['whyUsScreen'],
                                            'index' => $i,
                                        ])
                                        @endcomponent
                                    @endforeach
                                </div>
                                <hr />
                                <button class="btn btn-success pl-3 pr-3 why-scr-add-content-item" type="button">+
                                    Добавить блок</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col d-flex justify-content-end">
                    <input type="hidden" name="content-name" value="whyUsScreen" />
                    <button type="submit" class="btn btn-success align-self-end save-settings">
                        <div class="text">Сохранить изменения</div>
                        <div class="loader pb-4 d-none" style="width: 20px; height: 20px;"><img class="w-100"
                                src="{{ asset('uploads/loader.gif') }}"></div>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <div class="row pb-4">
        <div class="col">
            <form action="{{ route('admin.frontend.update') }}" method="post" onsubmit="return false;"
                class="site-settings">
                <div class="pb-4"></div>
                <h4>Экран "Партнеры"</h4>
                <div class="form-group pt-2 pb-1">
                    <label for="why-screen-title">Заголовок</label>
                    <input type="text" id='partners-screen-title' name='lang[header]' class="form-control"
                        value="{{ isset($langData) ? $langData['partnersScreen']['header'] : '' }}" />
                    <div class="pt-4"></div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group d-inline pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="partners-screen-heading1{{ isset($lang) ? "_$lang" : '' }}"
                                        {{ $settings['partnersScreen']['headerTag'] == 'h1' ? 'checked' : '' }}
                                        name="settings[headerTag]" value="h1">
                                    <label for="partners-screen-heading1{{ isset($lang) ? "_$lang" : '' }}">h1</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="partners-screen-heading2{{ isset($lang) ? "_$lang" : '' }}"
                                        {{ $settings['partnersScreen']['headerTag'] == 'h2' ? 'checked' : '' }}
                                        name="settings[headerTag]" value="h2">
                                    <label for="partners-screen-heading2{{ isset($lang) ? "_$lang" : '' }}">h2</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio"
                                        id="partners-screen-heading3{{ isset($lang) ? "_$lang" : '' }}"
                                        {{ $settings['partnersScreen']['headerTag'] == 'h3' ? 'checked' : '' }}
                                        name="settings[headerTag]" value="h3">
                                    <label for="partners-screen-heading3{{ isset($lang) ? "_$lang" : '' }}">h3</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio"
                                        id="partners-screen-heading4{{ isset($lang) ? "_$lang" : '' }}"
                                        {{ $settings['partnersScreen']['headerTag'] == 'h4' ? 'checked' : '' }}
                                        name="settings[headerTag]" value="h4">
                                    <label for="partners-screen-heading4{{ isset($lang) ? "_$lang" : '' }}">h4</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio"
                                        id="partners-screen-heading5{{ isset($lang) ? "_$lang" : '' }}"
                                        {{ $settings['partnersScreen']['headerTag'] == 'h5' ? 'checked' : '' }}
                                        name="settings[headerTag]" value="h5">
                                    <label for="partners-screen-heading5{{ isset($lang) ? "_$lang" : '' }}">h5</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio"
                                        id="partners-screen-heading6{{ isset($lang) ? "_$lang" : '' }}"
                                        {{ $settings['partnersScreen']['headerTag'] == 'h6' || $settings['partnersScreen']['headerTag'] == '' ? 'checked' : '' }}
                                        name="settings[headerTag]" value="h6">
                                    <label for="partners-screen-heading6{{ isset($lang) ? "_$lang" : '' }}">h6</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2">
                        <div>Фоновое изображение</div>
                        <div class="input-group">
                            <div class="w-100 position-relative" style="height: 100px;">
                                <label
                                    class='gallery-image-selector position-absolute d-flex justify-content-center align-items-center'
                                    for="partners-bg">
                                    <div
                                        class="w-100 position-absolute d-flex align-items-center justify-content-center upload-icon">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                    <img src="{{ asset(($settings['partnersScreen']['background']['path']??'').'/'.($settings['partnersScreen']['background']['filename']??'')) }}"
                                        class="w-100 gallery-img why-scr-icon-img" />
                                    <p class="text-black gallery-img-choose">Фоновое изображение</p>
                                </label>
                                <input class="gallery-image-file d-none" name="settings[background]" type="file" id='partners-bg' accept=".jpg,.jpeg,.png" />
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="form-group pt-5 pb-1">
                    <h4 class="pb-2">Логотипы</h4>
                    <div class="pt-2 pb-0 text-center partners-screen-content">
                        <div class="card card-default">
                            <div class="card-body">
                                <div id='partners-screen-logos'>
                                    @foreach ($settings['partnersScreen']['partners'] as $i => $icon)
                                        @component('admin.frontend.templates.partners-screen-item', [
                                            'item' => $icon,
                                            'langData' => isset($langData['partnersScreen']['partners'][$i]) ? $langData['partnersScreen']['partners'][$i] : null,
                                            'index' => $i,
                                        ])
                                        @endcomponent
                                    @endforeach
                                </div>
                                <hr />
                                <button class="btn btn-success pl-3 pr-3 partners-scr-add-logo-item" type="button">+</button>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="col d-flex justify-content-end">
                    <input type="hidden" name="content-name" value="partnersScreen" />
                    <button type="submit" class="btn btn-success align-self-end save-settings">
                        <div class="text">Сохранить изменения</div>
                        <div class="loader pb-4 d-none" style="width: 20px; height: 20px;"><img class="w-100"
                                src="{{ asset('uploads/loader.gif') }}"></div>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <div class="row pb-4">
        <div class="col">
            <form action="{{ route('admin.frontend.update') }}" method="post" onsubmit="return false;" class="site-settings">
                <div class="pb-4"></div>
                <h4>Экран "Новости"</h4>
                <div class="form-group pt-2 pb-1">
                    <label for="news-screen-header">Заголовок</label>
                    <input type="text" id='news-screen-header' name='lang[header]' class="form-control"
                        value="{{ $langData['newsScreen']['header'] }}" />
                    <div class="pt-4"></div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group d-inline pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="news-screen-heading1{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['newsScreen']['headerTag'] == 'h1' ? 'checked' : '' }}
                                        value="h1">
                                    <label for="news-screen-heading1{{ isset($lang) ? "_$lang" : '' }}">h1</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="news-screen-heading2{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['newsScreen']['headerTag'] == 'h2' ? 'checked' : '' }}
                                        value="h2">
                                    <label for="news-screen-heading2{{ isset($lang) ? "_$lang" : '' }}">h2</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="news-screen-heading3{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['newsScreen']['headerTag'] == 'h3' ? 'checked' : '' }}
                                        value="h3">
                                    <label for="news-screen-heading3{{ isset($lang) ? "_$lang" : '' }}">h3</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="news-screen-heading4{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['newsScreen']['headerTag'] == 'h4' ? 'checked' : '' }}
                                        value="h4">
                                    <label for="news-screen-heading4{{ isset($lang) ? "_$lang" : '' }}">h4</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="news-screen-heading5{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['newsScreen']['headerTag'] == 'h5' ? 'checked' : '' }}
                                        value="h5">
                                    <label for="news-screen-heading5{{ isset($lang) ? "_$lang" : '' }}">h5</label>
                                </div>
                            </div>
                            <div class="form-group d-inline pl-2 pr-2">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="news-screen-heading6{{ isset($lang) ? "_$lang" : '' }}"
                                        name="settings[headerTag]"
                                        {{ $settings['newsScreen']['headerTag'] == 'h6' || $settings['newsScreen']['headerTag'] == '' ? 'checked' : '' }}
                                        value="h6">
                                    <label for="news-screen-heading6{{ isset($lang) ? "_$lang" : '' }}">h6</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group pt-2 pb-1">
                    <label for="news-screen-seeall">Текст "Просмотреть все"</label>
                    <input type="text" id='news-screen-seeall' name='lang[seeAllText]' class="form-control"
                        value="{{ $langData['newsScreen']['seeAllText'] }}" />
                </div>
                <div class="col d-flex justify-content-end">
                    <input type="hidden" name="content-name" value="newsScreen" />
                    <button type="submit" class="btn btn-success align-self-end save-settings">
                        <div class="text">Сохранить изменения</div>
                        <div class="loader pb-4 d-none" style="width: 20px; height: 20px;"><img class="w-100"
                                src="{{ asset('uploads/loader.gif') }}"></div>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <div class="row pb-4">
        <div class="col">
            <form action="{{ route('admin.frontend.update') }}" method="post" onsubmit="return false;" class="site-settings">
                <div class="pb-4"></div>
                <h4>Footer</h4>
                <div class="form-group pt-2 pb-1">
                    <div class="row mb-3 col align-items-center">
                        <div class="col-2">
                            <div class="input-group">
                                <div class="w-100 position-relative" style="height: 100px;">
                                    <label
                                        class='gallery-image-selector position-absolute d-flex justify-content-center align-items-center'
                                        for="footer-logo">
                                        <div
                                            class="w-100 position-absolute d-flex align-items-center justify-content-center upload-icon">
                                            <i class="fas fa-upload"></i>
                                        </div>
                                        <img src="{{ asset(($settings['footer']['logo']['path']??'').'/'.($settings['footer']['logo']['filename']??'')) }}"
                                            class="w-100 gallery-img why-scr-icon-img" />
                                        <p class="text-black gallery-img-choose">Логотип</p>
                                    </label>
                                    <input class="gallery-image-file d-none" name="settings[logo]" type="file" id='footer-logo' accept=".jpg,.jpeg,.png,.mp4,.gif" />
                                </div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="input-group d-flex align-items-center">
                                <label for="gallery-image-icon-desc" class="m-0 pl-2 pr-2 gallery-img-icon-desc">Слоган:</label>
                                <input type="text" id='gallery-image-icon-desc'
                                    class="form-control gallery-icon-desc-input" name="lang[slogan]"
                                    value="{{ $langData['footer']['slogan'] }}" />
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="input-group d-flex align-items-center">
                                <label for="logo-title" class="m-0 pl-2 pr-2">Meta title: </label>
                                <input type="text" id='logo-title' class="form-control gallery-icon-desc-input"
                                    name="lang[metaTitle]" value="{{ $langData['footer']['metaTitle'] }}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col d-flex justify-content-end">
                    <input type="hidden" name="content-name" value="footer" />
                    <button type="submit" class="btn btn-success align-self-end save-settings">
                        <div class="text">Сохранить изменения</div>
                        <div class="loader pb-4 d-none" style="width: 20px; height: 20px;"><img class="w-100"
                                src="{{ asset('uploads/loader.gif') }}"></div>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
