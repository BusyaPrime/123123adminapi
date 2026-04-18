@push('scripts')
    <script type="text/javascript"
            src="{{ asset('vendor/bootstrap-show-password/bootstrap-show-password.min.js') }}"></script>
    <script>
        $(function () {
            $('.password').password({
                eyeClass: '',
                eyeOpenClass: 'icmn-eye',
                eyeCloseClass: 'icmn-eye-blocked'
            });
        });
    </script>
@endpush
