@if($data->count() > 0)
    <div class="row table-pagination">
        <div class="col-md-12">
            <div class="table-pagination__inner">
                @if(method_exists($data, 'total'))
                    <div class="paginate-info text-muted">
                        {{ trans('admin.pagination.showing') }} {{ $data->firstItem() }} {{ trans('admin.pagination.to') }} {{ $data->lastItem() }} {{ trans('admin.pagination.from') }} {{ $data->total() }} {{ trans('admin.pagination.entries') }}
                    </div>
                @endif

                @if($data->hasPages())
                    @php
                        $currentPage = $data->currentPage();
                        $lastPage = method_exists($data, 'lastPage') ? $data->lastPage() : $currentPage;
                        $windowStart = max(2, $currentPage - 2);
                        $windowEnd = min($lastPage - 1, $currentPage + 2);
                        $items = [];

                        if ($lastPage <= 12) {
                            for ($page = 1; $page <= $lastPage; $page++) {
                                $items[] = [
                                    'type' => 'page',
                                    'page' => $page,
                                ];
                            }
                        } else {
                            if ($currentPage <= 5) {
                                $windowStart = 2;
                                $windowEnd = 7;
                            } elseif ($currentPage >= $lastPage - 4) {
                                $windowStart = max(2, $lastPage - 6);
                                $windowEnd = $lastPage - 1;
                            }

                            $items[] = [
                                'type' => 'page',
                                'page' => 1,
                            ];

                            if ($windowStart > 2) {
                                $items[] = [
                                    'type' => 'jump',
                                    'page' => max(2, (int) floor((2 + ($windowStart - 1)) / 2)),
                                ];
                            }

                            for ($page = $windowStart; $page <= $windowEnd; $page++) {
                                $items[] = [
                                    'type' => 'page',
                                    'page' => $page,
                                ];
                            }

                            if ($windowEnd < $lastPage - 1) {
                                $items[] = [
                                    'type' => 'jump',
                                    'page' => min($lastPage - 1, (int) ceil((($windowEnd + 1) + ($lastPage - 1)) / 2)),
                                ];
                            }

                            $items[] = [
                                'type' => 'page',
                                'page' => $lastPage,
                            ];
                        }
                    @endphp

                    <nav class="table-pagination__nav" aria-label="Pagination">
                        <div class="table-pagination__list">
                            @if($data->onFirstPage())
                                <span class="table-pagination__item is-nav is-disabled" aria-disabled="true">« Назад</span>
                            @else
                                <a class="table-pagination__item is-nav" href="{{ $data->previousPageUrl() }}" rel="prev">« Назад</a>
                            @endif

                            @foreach($items as $item)
                                @if($item['type'] === 'jump')
                                    <a class="table-pagination__item is-jump" href="{{ $data->url($item['page']) }}" aria-label="Перейти к странице {{ $item['page'] }}">…</a>
                                @else
                                    @if($item['page'] === $currentPage)
                                        <span class="table-pagination__item is-active">{{ $item['page'] }}</span>
                                    @else
                                        <a class="table-pagination__item" href="{{ $data->url($item['page']) }}">{{ $item['page'] }}</a>
                                    @endif
                                @endif
                            @endforeach

                            @if($data->hasMorePages())
                                <a class="table-pagination__item is-nav" href="{{ $data->nextPageUrl() }}" rel="next">Вперёд »</a>
                            @else
                                <span class="table-pagination__item is-nav is-disabled" aria-disabled="true">Вперёд »</span>
                            @endif
                        </div>
                    </nav>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-md-12 text-center">
            <div class="paginate-info text-muted">
                {{trans('admin.pagination.no_entries')}}
            </div>
        </div>
    </div>
@endif
