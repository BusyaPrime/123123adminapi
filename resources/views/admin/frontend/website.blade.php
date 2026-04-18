@extends('admin.layout')

@section('center_content')
    @component('component.card', ['title' => 'Frontend', 'bodyClass' => 'card-body-no-padding'])
        <div class="row">
            <div class="col">
                {{-- - site title --}}
                {{-- - site meta description --}}
                {{-- - site additional meta --}}
                {{-- - first screen --}}
                {{-- - header with
                - header tag selection variant (h1 - h6) --}}
                {{-- - background
                - background image or video selection variant --}}
                {{-- - calculator
                - header
                - texts... --}}
                {{-- - third section
                - header with
                - header tag selection variant (h1 - h6)
                - left side text
                - image gallery with title and alt texts
                - fourth section
                    - header with
                    - header tag selection variant (h1 - h6)
                    - sub header
                    - advantages:
                        - header text with tag selection variant
                        - advantages descriptions:
                            - icon
                            - text
                        - background image
                        - background color selection
                - partners
                    - header text with tag selection (h1 - h6)
                    - partner icons 
                - news
                    - header text with tag selection (h1 - h6)
                    - see all text
                - footer
                - footer logo slogan --}}
                {{-- <pre>
                - social buttons
                    - social icon
                    - social button link
            </pre> --}}
            </div>
        </div>
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
                        <li class="nav-item" role="presentation">
                            <button class="nav-link pl-5 pr-5 mr-2" id="en_tab-btn" data-toggle="tab" data-target="#en_tab"
                                type="button" role="tab" aria-controls="en_tab" aria-selected="false">EN</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        @foreach ($languages as $langKey => $lang)
                            @component('admin.frontend.templates.tab-content', [
                                'lang' => $langKey,
                                'langData' => $lang,
                                'settings' => $settings,
                            ])
                            @endcomponent
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- hidden element for jquery --}}

        <div class="gallery-item-reserve d-none">
            @component('admin.frontend.templates.third-screen-item')
            @endcomponent
        </div>
        <div class="why-screen-item-reserve d-none">
            @component('admin.frontend.templates.why-screen-item')
            @endcomponent
        </div>

        <div class="why-scr-block-item-with-icon d-none">
            @component('admin.frontend.templates.why-us-block-item-with-icon')
            @endcomponent
        </div>
        <div class="partners-scr-block-item-with-icon d-none">
            @component('admin.frontend.templates.partners-screen-item')
            @endcomponent
        </div>

        <div class="popup-alert position-right-top">
            <div class="popup-container">
                <div class="popup-header">
                    <h6>Оповещение</h6>
                </div>
                <div class="popup-content">
                    <p class="popup-message">Popup message</p>
                </div>
            </div>
        </div>
    @endcomponent
@endsection



@push('scripts')
    <script src="{{ asset('assets/js/dropzone.min.js') }}" type='text/javascript'></script>
    <script>
        // Dropzone.options.myAwesomeDropzone = {
        //     maxFiles: 1,
        //     accept: function(file, done) {
        //         console.log("uploaded");
        //         done(true);
        //     },

        //     init: function() {
        //         this.on("addedfile", function() {
        //             if (this.files[1] != null) {
        //                 this.removeFile(this.files[0]);
        //             }
        //         });
        //     }
        // };
        // window.onbeforeunload = function(evt) {
        //     var message = 'Did you remember to download your form?';
        //     if (typeof evt == 'undefined') {
        //         evt = window.event;
        //     }
        //     if (evt) {
        //         evt.returnValue = message;
        //     }

        //     return message;
        // }
        // window.onclose = () => {
        //     return false;
        // };

        $3rdScrForm = $('.dropzone-third-screen-gallery div#third-screen-gallery');
        $whyScrForm = $('div.why-screen-content div#why-screen-content');
        $partnersContent = $('div.partners-screen-content div#partners-screen-logos');


        const sortableOptions = (itemSelector) => ({
            items: itemSelector,
            cursor: 'grab',
            opacity: 0.5,
            distance: 2,
            tolerance: 'pointer',
        });

        $3rdScrForm.sortable(sortableOptions('.gallery-photo-item'));
        $whyScrForm.sortable(sortableOptions('.why-screen-content-item'));
        $('div.why-us-block-items-with-icon').sortable(sortableOptions('.why-us-block-item-with-icon'));

        $galleryItem = $('.gallery-item-reserve.d-none .gallery-photo-item');
        $whyScrItem = $('div.why-screen-item-reserve.d-none .why-screen-content-item');
        $whyScrSubItem = $('div.why-scr-block-item-with-icon.d-none div.why-us-block-item-with-icon');
        $partnerItem = $('div.partners-scr-block-item-with-icon.d-none div.partners-block-item');

        const onClickItemDelete = (e, selector) => {
            $btn = $(e.currentTarget);
            $btn.closest(selector).remove();
        }

        const onChangeImage = (e) => {
            $input = $(e.currentTarget);
            $parent = $input.parent();
            $imgEl = $parent.find('img');

            const [file] = $input.get(0).files;

            var reader = new FileReader();
            reader.onload = function(e) {
                $imgEl.attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }

        $('.third-screen-gallery-add-item').on('click', (e) => {
            $item = $galleryItem.clone();
            i = $($3rdScrForm[0]).children().length;
            if (i > 10) return;

            $imgLabel = $item.find('label.gallery-image-selector');
            $imgInput = $item.find('input.gallery-image-file');
            $img = $item.find('img.gallery-img');
            
            $defaultInput = $item.find('input.default-image');

            $defaultInput.attr('name', $defaultInput.attr('name').replace('[]', `[${i}]`));

            // $($item.find('input.default-image')).attr('name', $($item.find('input.default-image')).attr('name').replace('[]', `[${i}]`));

            $labelDesc = $item.find('label.gallery-img-desc-selector');
            $inputDesc = $item.find('input.gallery-img-desc');

            $labelAlt = $item.find('label.gallery-img-alt-selector');
            $inputAlt = $item.find('input.gallery-img-alt');
            //setting new values...
            $imgInput.attr({
                'id': `${$imgInput.attr('id')}_${i}`,
                'name': $imgInput.attr('name').replace('[]', `[${i}]`)
            });
            $imgInput.on('change', onChangeImage);
            $imgLabel.attr('for', $imgInput.attr('id'));
            $img.attr('src', '');

            $inputDesc.attr({
                'id': `${$inputDesc.attr('id')}_${i}`,
                'name': `${$inputDesc.attr('name').replace('[]', `[${i}]`)}`
            });
            $labelDesc.attr('for', $inputDesc.attr('id'));

            $inputAlt.attr({
                'id': `${$inputAlt.attr('id')}_${i}`,
                'name': `${$inputAlt.attr('name').replace('[]', `[${i}]`)}`
            });
            $labelAlt.attr('for', $inputAlt.attr('id'));

            $item.find('button.delete-image').on('click', (e) => onClickItemDelete(e, '.gallery-photo-item'));

            $3rdScrForm.append($item);
            $3rdScrForm.sortable('refresh');
        });

        $('.why-scr-add-content-item').on('click', () => {
            $item = $whyScrItem.clone();
            const i = $($whyScrForm[0]).children().length;
            if (i > 4) return;
            $item.attr('data-order', i);

            $imgLabel = $item.find('label.gallery-image-selector');
            $imgInput = $item.find('input.gallery-image-file');
            $imgInputHidden = $item.find('input.img-input-hidden');
            $img = $item.find('img.gallery-img');

            $labelDesc = $item.find('label.gallery-img-desc-selector');
            $inputDesc = $item.find('input.gallery-img-desc');

            $labelAlt = $item.find('label.gallery-img-alt-selector');
            $inputAlt = $item.find('input.gallery-img-alt');
            
            $labelColor = $item.find('label.gallery-bg-color-label');
            $inputColor = $item.find('input.gallery-bg-color-input');

            // //setting new values...
            $imgInput.attr({
                'id': `${$imgInput.attr('id')}_${i}`,
                'name': `${$imgInput.attr('name').replace('[index]', `[${i}]`)}`
            });
            
            $imgInputHidden.attr({
                'id': `${$imgInputHidden.attr('id')}_${i}`,
                'name': `${$imgInputHidden.attr('name').replace('[index]', `[${i}]`)}`
            });
            $imgInput.on('change', onChangeImage);
            $imgLabel.attr('for', $imgInput.attr('id'));
            $img.attr('src', '');

            $inputDesc.attr({
                'id': `${$inputDesc.attr('id')}_${i}`,
                'name': `${$inputDesc.attr('name').replace('[index]', `[${i}]`)}`
            });
            $labelDesc.attr('for', $inputDesc.attr('id'));

            $inputAlt.attr({
                'id': `${$inputAlt.attr('id')}_${i}`,
                'name': `${$inputAlt.attr('name').replace('[index]', `[${i}]`)}`
            });
            $labelAlt.attr('for', $inputAlt.attr('id'));
            
            $inputColor.attr({
                'id': `${$inputColor.attr('id')}_${i}`,
                'name': `${$inputColor.attr('name').replace('[index]', `[${i}]`)}`
            });
            $labelColor.attr('for', $inputColor.attr('id'));

            $item.find('button.why-scr-content-delete').on('click', (e) => onClickItemDelete(e,
                '.why-screen-content-item'));
            $item.find('button.add-subitem-button').on('click', onClickAddSubitem);

            $whyScrForm.append($item);
            $whyScrForm.sortable('refresh');
        });

        $('button.why-scr-content-delete').on('click', (e) => onClickItemDelete(e, '.why-screen-content-item'));
        $('button.why-scr-icon-delete').on('click', (e) => onClickItemDelete(e, '.why-us-block-item-with-icon'));
        $('button.delete-image').on('click', (e) => onClickItemDelete(e, '.gallery-photo-item'));
        $('input.gallery-image-file').on('change', onChangeImage);

        const onClickAddSubitem = (e) => {
            const $item = $whyScrSubItem.clone();
            const $whyScrSubItemsCont = $(e.currentTarget).parent().find('div.why-us-block-items-with-icon');
            const order = $(e.currentTarget).closest('.why-screen-content-item').data('order');
            const i = $($whyScrSubItemsCont[0]).children().length;
            if (i > 8) return;

            $imgLabel = $item.find('label.gallery-image-selector');
            $imgInput = $item.find('input.gallery-image-file');
            $imgInputHidden = $item.find('input.img-subitem-input-hidden');
            $img = $item.find('img.gallery-img');

            $labelDesc = $item.find('label.gallery-img-icon-desc');
            $inputDesc = $item.find('input.gallery-icon-desc-input');

            // setting new values...
            $imgInput.attr({
                'id': `${$imgInput.attr('id')}_${order}_${i}`,
                'name': $imgInput.attr('name').replace('[mainIndex]', `[${order}]`).replace('[index]', `[${i}]`)
            });
            
            $imgInputHidden.attr({
                'name': $imgInputHidden.attr('name').replace('[mainIndex]', `[${order}]`).replace('[index]', `[${i}]`)
            });

            $imgInput.on('change', onChangeImage);
            $imgLabel.attr('for', $imgInput.attr('id'));
            $img.attr('src', '');

            $inputDesc.attr({
                'id': `${$inputDesc.attr('id')}_${order}_${i}`,
                'name': $inputDesc.attr('name').replace('[mainIndex]', `[${order}]`).replace('[index]', `[${i}]`)
            });
            $labelDesc.attr('for', $inputDesc.attr('id'));

            $item.find('button.why-scr-icon-delete').on('click', (e) => onClickItemDelete(e,
                '.why-us-block-item-with-icon'));
            $whyScrSubItemsCont.append($item);
            $whyScrSubItemsCont.sortable(sortableOptions('.why-us-block-item-with-icon'));
        }

        const onClickAddPartnerBtn = (e) => {
            const $item = $partnerItem.clone();
            const i = $($partnersContent[0]).children().length;
            if (i + 1 > 25) return;

            $imgLabel = $item.find('label.gallery-image-selector');
            $imgInput = $item.find('input.gallery-image-file');
            $imgInputDefault = $item.find('input.gallery-image-file-default');
            $img = $item.find('img.gallery-img');

            $labelDesc = $item.find('label.gallery-img-icon-desc');
            $inputDesc = $item.find('input.gallery-icon-desc-input');

            // // //setting new values...
            $imgInput.attr({
                'id': `${$imgInput.attr('id')}_${i}`,
                'name': $imgInput.attr('name').replace('[index]', `[${i}]`),
            });
            $imgInputDefault.attr({
                'name': $imgInputDefault.attr('name').replace('[index]', `[${i}]`),
            });
            $imgInput.on('change', onChangeImage);
            $imgLabel.attr('for', $imgInput.attr('id'));
            $img.attr('src', '');

            $inputDesc.attr({
                'id': `${$inputDesc.attr('id')}_${i}`,
                'name': $inputDesc.attr('name').replace('[index]', `[${i}]`)
            });
            $labelDesc.attr('for', $inputDesc.attr('id'));

            $item.find('button.partners-scr-icon-delete').on('click', (e) => onClickItemDelete(e,
                '.partners-block-item'));
            $partnersContent.append($item);
            $partnersContent.sortable(sortableOptions('.partners-block-item'));
        }

        $('button.add-subitem-button').on('click', onClickAddSubitem);
        $('div.partners-screen-content button.partners-scr-add-logo-item').on('click', onClickAddPartnerBtn);
        $('button.partners-scr-icon-delete').on('click', (e) => onClickItemDelete(e, '.partners-block-item'));

        const submitForm = (e) => {
            const $popup = $('div.popup-alert');
            const currentForm = e.currentTarget;
            const $button = $(currentForm).find('button.save-settings');
            const $content = $popup.find('p.popup-message');
            $popup.removeClass(['active', 'error']);

            const showPopup = () => {
                $popup.show(300, 'easeOutSine');
                setTimeout(() => {
                    $popup.hide(300, 'easeOutSine');
                }, 5000);
            }


            // $formInputs = $(this).find('input');
            
            // for(let i = 0; i < $formInputs.length; i++){
            //     if($($formInputs[i]).val() == ''){
            //         $content.text('Заполните все поля / Файлы');
            //         $popup.removeClass('success');
            //         $popup.addClass('error');
            //         showPopup();
            //         return;
            //     }
            // }
            
            tinymce.triggerSave();
            $button.find('.text').addClass(['d-none']);
            $button.find('.loader').removeClass(['d-none']);
            const lang = $(currentForm).closest('div.tab-content').data('lang');

            let formData = new FormData(e.currentTarget);
            formData.append('language', lang);

            // for(const [key, value] of formData){
            //     console.log(`key ${key}, value: ${value}`);
            // }

            $.ajax({
                url: $(currentForm).attr('action'),
                method: 'post',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
            }).done((res) => {
                console.log(res);
                $content.text(res.message);
                $popup.addClass('success');
                showPopup();
                $button.find('.text').removeClass(['d-none']);
                $button.find('.loader').addClass(['d-none']);
            }).catch((err) => {
                $content.text(res.message);
                $popup.addClass('error');
                showPopup();
            });

            e.preventDefault();
        }

        const $settingsForm = $('form.site-settings').map((index, el) => {
            $(el).on('submit', submitForm)
        });

        $settingsForm.submit();

















        // const THRD_SCR_GLRY = 'thirdScreenGallery';
        // const $thrdScrGlryProgTot = $('div.fileupload-process-gallery .progress-bar.progress-bar-success');

        // const thirdScreenGallery = new Dropzone('.dropzone-third-screen-gallery', {
        //     url: "{{ route('admin.frontend.uploadWebsiteFiles') }}",
        //     maxFiles: 10,
        //     init: () => {
        //         const dz = this.Dropzone.instances.find((e) => e.element.classList.contains('dropzone-third-screen-gallery'));
        //         console.log(dz);
        //         const files = [
        //             {name: 'square-600', size: 1000, url: 'https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_960_720.jpg'},
        //             {name: 'square-800', size: 1200, url: 'https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_960_720.jpg'}
        //         ];

        //         files.forEach(function (file) {

        //             let mockFile = {
        //                 name: file.name,
        //                 size: file.size,
        //             };

        //             dz.displayExistingFile(
        //                 mockFile,
        //                 file.url,
        //                 () => {},
        //                 'anonymous'
        //             );
        //         });

        //     },
        //     autoProcessQueue: false,
        //     previewsContainer: '.files.dropzone-previews',
        //     previewTemplate: '<div class="row mt-2 dz-image-preview"><div class="col-auto"><span class="preview"><img src="" data-dz-thumbnail="" /></span></div><div class="col d-flex align-items-center"><p class="mb-0">&nbsp;<span class="lead pr-4" data-dz-name=""></span>(<span data-dz-size=""></span>)</p><strong class="error text-danger" data-dz-errormessage=""></strong></div><div class="col-6 d-flex align-items-center"><div class="progress progress-striped active w-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress=""></div></div></div><div class="col-auto d-flex align-items-center"><div class="btn-group"><button data-dz-remove="" class="btn btn-danger delete"><i class="fas fa-trash"></i>&nbsp;&nbsp;<span>Delete</span></button></div></div></div>',
        //     clickable: '.fileinput-button.dz-clickable',
        //     withCredentials: true,
        //     acceptedMimeTypes: 'image/jpeg,image/jpg,image/png,image/webp,video/mp4,video/gif',
        //     accept: (file, done) => {
        //         $thrdScrGlryProgTot.css('width', 0);
        //         done();
        //     },
        //     params: {
        //         insertInto: THRD_SCR_GLRY
        //     },
        //     totaluploadprogress: (perc, bytes) => {
        //         if(bytes > 0) $thrdScrGlryProgTot.css('width', `${perc}%`);
        //     },
        //     removedfile: (file) => {
        //         if(null != file.status && (file.status == 'queued' || file.status == 'canceled')){
        //             thirdScreenGallery._updateMaxFilesReachedClass();
        //             $thrdScrGlryProgTot.css('width', '0');
        //             $(file.previewElement).remove();
        //         }

        //         if(null != file.status && file.status == 'success' && null != file.xhr && file.xhr.status == 200){
        //             const filename = JSON.parse(file.xhr.response).filename;
        //             $.ajax({
        //                 url: "{{ route('admin.frontend.deleteWebsiteFile') }}",
        //                 method: 'POST',
        //                 data: ({
        //                     removeFrom: THRD_SCR_GLRY,
        //                     filename: filename
        //                 }),
        //                 success: () => {
        //                     // thirdScreenGallery._updateMaxFilesReachedClass();
        //                     // $thrdScrGlryProgTot.css('width', '0%');
        //                     $(file.previewElement).remove();
        //                 }
        //             });
        //         }

        //     },
        // });

        // // const files = [
        // //     {name: 'square-600', size: 1000, url: 'https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_960_720.jpg'},
        // //     {name: 'square-800', size: 1200, url: 'https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885_960_720.jpg'}
        // // ];

        // // files.forEach(function (file) {

        // //     let mockFile = {
        // //         name: file.name,
        // //         size: file.size,
        // //     };

        // //     thirdScreenGallery.displayExistingFile(
        // //         mockFile,
        // //         file.url,
        // //         () => {},
        // //         'anonymous'
        // //     );
        // // });


        // $('button.dz-start-load').on('click', () => {
        //     thirdScreenGallery.processQueue();
        // });
    </script>
@endpush
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dropzone.min.css') }}" type="text/css" />
@endpush
