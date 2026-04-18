{{--{{ $bodyClass ?? '' }}--}}
@if(isset($title))
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 card-header">
            <div class="{{ $title_class ?? 'col-sm-6' }}">
                <h1>{!! $title ?? '' !!}</h1>
            </div>
            {{ $buttons ?? '' }}
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
@endif
{{ $filters ?? '' }}
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card-body">
                    {{ $slot }}
                </div>
                <!-- /.card-body -->
                {{ $bottom ?? ''}}
                <!-- /.card -->
            </div>

        </div>

    </div>
    <!-- /.container-fluid -->
</section>
