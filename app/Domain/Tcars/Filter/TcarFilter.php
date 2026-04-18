<?php


namespace App\Domain\Tcars\Filter;


use App\Services\FilterService\Filter;
use Illuminate\Http\Request;

class TcarFilter extends Filter
{

    /**
     * Доступные фильтры.
     *
     * @var array
     */
    protected $available = [
        'id', 'model', 'number', 'user_id',
        'sort', 'perPage'
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
        $this->addSortable('model');
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

    public function user_id($value)
    {
        return $this->builder->where($this->column('user_id'), $value);
    }

    /**
     * Поиск по имени.
     *
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function model($value)
    {
        return $this->builder->where($this->column('model'), 'like', '%' . $value . '%');
    }

    public function number($value)
    {
        return $this->builder->where($this->column('number'), 'like', '%' . $value . '%');
    }
}
