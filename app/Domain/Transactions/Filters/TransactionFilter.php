<?php

namespace App\Domain\Transactions\Filters;

use App\Services\FilterService\Filter;
use Illuminate\Http\Request;

class TransactionFilter extends Filter
{

    /**
     * Доступные фильтры.
     *
     * @var array
     */
    protected $available = [
        'id', 'user_id', 'company_id',
        'type', 'date_start', 'date_end',
        'sort', 'perPage'
    ];


    /**
     * Фильтры по умолчанию.
     *
     * @var array
     */
    protected $defaults = [
        'sort' => '-id',
        'id' => '',
        'user_id' => '',
        'company_id' => '',
        'type' => '',
        'date_start' => '',
        'date_end' => '',
    ];

    public function __construct(Request $request)
    {
        $this->input = $this->prepareInput($request->all());
    }

    /**
     * Инициализация фильтра.
     */
    protected function init()
    {
        //Добавляем поля для сортировки
        $this->addSortable('id');
    }

    /**
     * Поиск по id.
     *
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('id'), $value);
        }
    }

    public function user_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('user_id'), $value);
        }
    }

    public function company_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('company_id'), $value);
        }
    }

    public function type($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('type'), $value);
        }
    }
}
