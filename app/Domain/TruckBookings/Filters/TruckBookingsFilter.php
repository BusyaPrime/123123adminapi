<?php


namespace App\Domain\TruckBookings\Filters;


use App\Services\FilterService\Filter;
use Illuminate\Http\Request;

class TruckBookingsFilter extends Filter
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
        'id', 'name', 'driver_id', 'user_id',  'company_id', 'client_company_id', 'cargo_type_id', 'region_from_id', 'region_to_id', 'created_at', 'status',
        'rating', 'driver_rating', 'user_phone','driver_phone',
        'date_start', 'date_end',
        'sort', 'perPage', 'car_type'
    ];


    /**
     * Фильтры по умолчанию.
     *
     * @var array
     */
    protected $defaults = [
        'sort' => '-id',
        'id' => '',
        'name' => '',
        'region_from_id' => '',
        'driver_id' => '',
        'user_id' => '',
        'region_to_id' => '',
        'cargo_type_id' => '',
        'date_start' => '',
        'date_end' => '',
        'user_phone' => '',
        'company_id' => '',
        'client_company_id' => '',
        'driver_phone' => '',
        'status' => '',
        'car_type' => '',
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
        $this->addSortable('weight');
        $this->addSortable('price');
        $this->addSortable('rating');
        $this->addSortable('driver_rating');
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

    public function company_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('company_id'), $value);
        }
    }

    public function car_type($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('car_type_id'), $value);
        }
    }

    public function name($value)
    {
        if ($value != '') {
            return $this->builder->whereHas('user', function ($query) use ($value) {
                $query->where('name', 'like', '%' . $value . '%')
                    ->orWhere('surname', 'like', '%' . $value . '%')
                    ->orWhere('middle_name', 'like', '%' . $value . '%');
            });
        }
    }

    public function user_phone($value)
    {
        if ($value != '') {
            return $this->builder->whereHas('user', function ($query) use ($value) {
                $query->whereHas('user', function ($query) use ($value) {
                    $query->where('username', 'like', '%' . $value . '%');
                });
            });
        }
    }

    public function driver_phone($value)
    {
        if ($value != '') {
            return $this->builder->whereHas('driver', function ($query) use ($value) {
                $query->whereHas('user', function ($query) use ($value) {
                    $query->where('username', 'like', '%' . $value . '%');
                });
            });
        }
    }

    public function cargo_type_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('cargo_type_id'), $value);
        }
    }

    public function region_from_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('region_from_id'), $value);
        }
    }

    public function region_to_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('region_to_id'), $value);
        }
    }

    public function driver_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('driver_id'), $value);
        }
    }

    public function user_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('user_id'), $value);
        }
    }

    public function status($value)
    {
        if ($value != '') {
            if($value == 'in_progress') {
                return $this->builder->whereIn($this->column('status'), ['waiting', 'accepted', 'arrived', 'processing', 'pause']);
            }
            if($value == 'free') {
                return $this->builder->whereIn($this->column('status'), ['new', 'order']);
            }
            return $this->builder->where($this->column('status'), $value);
        }
    }
    
    public function client_company_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('client_company_id'), $value);
        }
    }

    public function created_at($value)
    {
        if ($value != '') {
            $value = date('Y-m-d', strtotime($value));
            return $this->builder->whereBetween($this->column('created_at'), [$value . ' 00:00:00', $value . ' 23:59:59']);
        }
        return $this->builder;
    }
}
