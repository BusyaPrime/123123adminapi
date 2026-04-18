@push('scripts')
    <script type="text/javascript"
            src="{{ asset('vendor/inputmask/jquery.inputmask.bundle.js') }}"></script>
    <script>
        $(function () {
            $('.time').inputmask({regex: "([01][0-9]|2[0-3]):[0-5][0-9]"});
        });

    </script>
@endpush
