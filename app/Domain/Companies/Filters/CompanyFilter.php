<?php

namespace App\Domain\Companies\Filters;

use App\Services\FilterService\Filter;
use Illuminate\Http\Request;

class CompanyFilter extends Filter
{
    /**
     * Доступные фильтры.
     *
     * @var array
     */
    protected $available = [
        'id', 'title',
        'contract_number',
        'created_at',
        'active',
        'sort',
        'perPage',
        'role'
    ];

    /**
     * Фильтры по умолчанию.
     *
     * @var array
     */
    protected $defaults = [
        'sort' => '-id',
        'created_at' => '',
        'active' => '',
        'title' => '',
        'role' => '',
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
        $this->addSortable('title');
        $this->addSortable('active');
    }

    /**
     * Поиск по id.
     *
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function id($value)
    {
        return $this->builder->where($this->column('id'), $value);
    }

    public function title($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('title'), 'like', '%'.$value.'%');
        }
    }

    public function contract_number($value)
    {
        return $this->builder->where($this->column('contract_number'), 'like', '%'.$value.'%');
    }


    public function active($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('active'), $value);
        }
    }
    
    public function role($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('role'), $value);
        }
    }

    public function created_at($value)
    {
        if ($value != '') {
            return $this->builder->whereBetween($this->column('created_at'), [date('Y-m-d 00:00:00', strtotime($value)), date('Y-m-d 23:59:59', strtotime($value))]);
        }
    }

    public function sort($value, $concat = null)
    {
        if ($value != '' && ($value == '-cars' || $value == 'cars')) {
            $firstChar = substr($value, 0, 1);
            if ($firstChar === '-') {
                $type = 'desc';
                $value = substr($value, 1);
            } else {
                $type = 'asc';
            }

            $this->builder->withCount('users')->orderBy('users_count', $type);
        }
        return parent::sort($value, $concat);
    }

}
