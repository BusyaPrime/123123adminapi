@push('scripts')
    <script type="text/javascript"
            src="{{ asset('vendor/inputmask/jquery.inputmask.bundle.js') }}"></script>
    <script>
        $(function () {
            $('.phone-input').inputmask("+\\9\\9\\8 (99) 999-99-99");

            const errorBox = document.querySelector('.phone-input');
            const error = $('<div style="color:#fe0032;font-size:11px;"> </div>');
            $(errorBox.parentElement.parentElement).append(error);
            
            $('.phone-input').on('blur', function(e) {
                const phone = e.currentTarget.value;
                // +998 (11) 221-32-22
                const regExp = /^(\s*)?(\+)?([- _():=+]?\d[- _():=+]?){12,14}(\s*)?$/;
                // const regExp = /[a-zA-Z]*[0-9]*[\.\_]*/;

                console.log(phone);
                
                if(!phone.match(regExp)){
                    $(error).text('{{ trans("admin.error_phone") }}');
                    $('.register-btn').attr('disabled', 'disabled');
                }
                else{
                    $(error).text('');
                    $('.register-btn').removeAttr('disabled');
                }
            });
        });
    </script>
@endpush
