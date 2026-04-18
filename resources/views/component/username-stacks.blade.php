@push('scripts')
    <script>
        $(function () {
            const errorBox = document.querySelector('.login-input');
            const error = $('<div style="color:#fe0032;font-size:11px;"> </div>');
            $(errorBox.parentElement.parentElement).append(error);
            
            $('.login-input').on('keyup', function(e) {
                const username = e.currentTarget.value;
                const regExp = /^([a-z]?([-_.]?[a-z0-9]+)+)$/i;
                // const regExp = /[a-zA-Z]*[0-9]*[\.\_]*/; 
                
                if(!username.match(regExp)){
                    $(error).text('{{ trans("admin.error_username") }}');
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
