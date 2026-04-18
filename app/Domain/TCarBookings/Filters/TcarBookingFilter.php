<?php


namespace App\Domain\TCarBookings\Filters;


use App\Services\FilterService\Filter;
use Illuminate\Http\Request;

class TcarBookingFilter extends Filter
{


    /**
     * Доступные фильтры.
     *
     * @var array
     */
    protected $available = [
        'id', 'user_id', 'driver_id',
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
    public function driver_id($value)
    {
        return $this->builder->where($this->column('driver_id'), $value);
    }
}
