<?php


namespace App\Domain\Tcompanies\Filters;


use App\Services\FilterService\Filter;
use Illuminate\Http\Request;

class TCompanyFilter extends Filter
{

    /**
     * Доступные фильтры.
     *
     * @var array
     */
    protected $available = [
        'id', 'title', 'contract_number',  'sort', 'perPage'
    ];

    /**
     * Фильтры по умолчанию.
     *
     * @var array
     */
    protected $defaults = [
        'sort' => '-id'
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
        return $this->builder->where($this->column('title'), 'like', '%'.$value.'%');
    }

    public function contract_number($value)
    {
        return $this->builder->where($this->column('contract_number'), 'like', '%'.$value.'%');
    }
}
