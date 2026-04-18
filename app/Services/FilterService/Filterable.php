<?php

namespace App\Services\FilterService;

trait Filterable {

    /**
     * @var Filter|null
     */
    protected $filters;

    /**
     * Подготовленный запрос для фильрации
     *
     * @param $query \Illuminate\Database\Eloquent\Builder
     * @param Filter|null $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, ?Filter $filters)
    {
        $this->filters = $filters;

        return $filters->apply($query);
    }

    /**
     * Добавляет к пагинации параметры фильтров
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function scopePaginateFilter($query, $perPage = 20/*, $columns = ['*'], $pageName = 'page', $page = null*/)
    {
        $paginator = $query->paginate($perPage/*, $columns, $pageName, $page*/);

        if ($this->filters !== null) {
            $paginator->appends($this->filters->filters());
        }

        return $paginator;
    }

    /**
     * Добавляет к пагинации параметры фильтров
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function scopeSimplePaginateFilter($query, $perPage = 20/*, $columns = ['*'], $pageName = 'page'*/)
    {
        $paginator = $query->simplePaginate($perPage/*, $columns, $pageName*/);

        if ($this->filters !== null) {
            $paginator->appends($this->filters->filters());
        }

        return $paginator;
    }
}
