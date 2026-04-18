@push('scripts')
    <script type="text/javascript"
            src="{{ asset('vendor/inputmask/jquery.inputmask.bundle.js') }}"></script>
    <script>
        $(function () {
            $('.amount').inputmask({alias: 'decimal', groupSeparator: ' ', autoGroup: true, allowMinus: false});
        });

    </script>
@endpush
@push('styles')
    <style>
        .amount {
            text-align: left!important;
        }
    </style>
@endpush
