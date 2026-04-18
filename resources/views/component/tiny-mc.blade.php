
@push('scripts')
    <script src="{{ asset('vendor/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script>
        var editor_config = {
            // path_absolute : "{{ route('admin.home').'/' }}",
            language: 'ru',
            selector: "textarea.text-editor",
            // plugins: [
            //     "advlist autolink lists link charmap print preview hr anchor pagebreak",
            //     "searchreplace wordcount visualblocks visualchars code fullscreen",
            //     "insertdatetime nonbreaking table contextmenu directionality",
            //     "emoticons template paste textcolor colorpicker textpattern"
            // ],
            // toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
            // relative_urls: false,
            // file_browser_callback : function(field_name, url, type, win) {
            //     var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            //     var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

            //     var cmsURL = editor_config.path_absolute + 'filemanager?field_name=' + field_name;
            //     if (type == 'image') {
            //         cmsURL = cmsURL + "&type=Images";
            //     } else {
            //         cmsURL = cmsURL + "&type=Files";
            //     }

            //     tinyMCE.activeEditor.windowManager.open({
            //         file : cmsURL,
            //         title : 'Загрузить файл',
            //         width : x * 0.8,
            //         height : y * 0.9,
            //         resizable : "yes",
            //         close_previous : "no"
            //     });
            // },
            invalid_elements : "img, video, script",
            setup: function(ed) {
                ed.on('change', function(e) {
                    tinymce.triggerSave();
                });
            },
            height: '300px'
        };

        $(() => {
            tinymce.init(editor_config);
        });
    </script>
@endpush
