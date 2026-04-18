<?php


namespace App\Domain\TruckBookings\Filters;


use App\Services\FilterService\Filter;
use Illuminate\Http\Request;

class BookingPriceOffersFilter extends Filter
{

    /**
     * Доступные фильтры.
     *
     * name
     * region_from_id
     * region_to_id
     * cargo_type_id
     *
     * status (in)
     * sort: id / weight / price
     *
     * @var array
     */
    protected $available = [
        'id', 'amount', 'driver_id', 'booking_id', 'sort', 'perPage', 'driver_id'
    ];


    /**
     * Фильтры по умолчанию.
     *
     * @var array
     */
    protected $defaults = [
        'sort' => '-id',
        'amount' => '',
        'id' => '',
        'driver_id' => '',
        'booking_id' => ''
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
        $this->addSortable('amount');
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
    public function amount($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('amount'), $value);
        }
    }
    public function booking_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('booking_id'), $value);
        }
    }
    public function driver_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('driver_id'), $value);
        }
    }
}
