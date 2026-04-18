@push('scripts')
    <script type="text/javascript"
            src="{{ asset('vendor/inputmask/jquery.inputmask.bundle.js') }}"></script>
    <script>
        $(function () {
            $('.phone-input').inputmask("+\\9\\9\\8 (99) 999-99-99");
        });
    </script>
@endpush
