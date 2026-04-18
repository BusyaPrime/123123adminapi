@push('scripts')
    <script>
        $(function () {
            $('#{{$wrapperId}}').on('click', '.{{$removeClass}}', function () {
                @if(isset($min) && isset($element))
                if($('#{{$wrapperId}}').find('.{{$element}}').length > {{$min}}) {
                    $(this).parent().remove();
                } else {
                    $('#{{$wrapperId}}').find('input').val('');
                }
                @else
                    $(this).parent().remove();
                @endif
            });
            $('.{{$addClass}}').on('click', function () {
                if($('#{{$wrapperId}}').find('.{{$element}}').length > 0) {
                    var copyElem = $($('#{{$wrapperId}}').find('.{{$element}}')[0]).clone();
                    copyElem.find('input').val('');

                    @if(isset($inputmask) && $inputmask == true)
                    copyElem.find('input').inputmask('{{$inputmask}}');
                    @endif

                    $('#{{$wrapperId}}').append(copyElem);
                }
            });
        });
    </script>
@endpush
