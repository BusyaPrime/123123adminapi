<?php $all_locales = LaravelLocalization::getSupportedLanguagesKeys(); ?>
<?php $default = LaravelLocalization::getDefaultLocale(); ?>
@if(count($all_locales) > 1)
<ul class="nav nav-tabs lang-nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link active" href="#{{$default}}{{$label??''}}" data-toggle="list" role="tab">
            {{--<img src="{{ asset('images/flag-'.$default.'.png') }}" alt="{{$default}}" class="img-fluid">--}}
            @lang('admin.locales.'.$default)
        </a>
    </li>
    @foreach($all_locales as $locale)
        @if($locale != $default)
            <li class="nav-item">
                <a class="nav-link"  data-toggle="list" href="#{{$locale}}{{$label??''}}" role="tab">
                    {{--<img src="{{ asset('images/flag-'.$locale.'.png') }}" alt="{{$locale}}" class="img-fluid">--}}
                    @lang('admin.locales.'.$locale)
                </a>
            </li>
        @endif
    @endforeach
</ul>
@endif

<div class="tab-content  mb-5">
    <div class="tab-pane active" id="{{$default}}{{$label??''}}" role="tabpanel">
        @include($form, ['lang' => $default, 'model' => $model])
        <input type="hidden" name="translations[{{ $default }}][locale]" value="{{ $default }}">
    </div>
    @foreach($all_locales as $locale)
        @if($locale != $default)
            <div class="tab-pane" id="{{$locale}}{{$label??''}}" role="tabpanel">
                @include($form, ['lang' => $locale, 'model' => $model])
                <input type="hidden" name="translations[{{ $locale }}][locale]" value="{{ $locale }}">
            </div>
        @endif
    @endforeach
</div>
