<?php


namespace App\Domain\Cars\Filter;


use App\Services\FilterService\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarFilter extends Filter
{

    /**
     * Доступные фильтры.
     *
     * @var array
     */
    protected $available = [
        'id', 'model', 'number', 'user_id', 'username',
        'name',
        'car_type_id',
        'dimension_x',
        'dimension_y',
        'dimension_z',
        'company_id',
        'max_weight',
        'active',
        'moderated',
        'sort', 'perPage'
    ];


    /**
     * Фильтры по умолчанию.
     *
     * @var array
     */
    protected $defaults = [
        'sort' => '-id',
        'active' => '',
        'moderated' => '',
        'name' => '',
        'username' => '',
        'company_id' => '',
        'user_id' => '',
        'car_type_id' => '',
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
        $this->addSortable('created_at');
        $this->addSortable('active');

        $this->addJoin('user_profiles', function () {
            $this->builder->leftJoin('user_profiles', function ($join) {
                /**
                 * @var $join \Illuminate\Database\Query\JoinClause
                 */
                $join->on($this->table . '.user_id', 'user_profiles' . '.user_id');
            })
                ->select([$this->table . '.*', 'user_profiles' . '.rating'])
            ;
        });
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

    public function dimension_x($value)
    {
        return $this->builder->where($this->column('dimension_x'), '>=', $value);
    }

    public function dimension_y($value)
    {
        return $this->builder->where($this->column('dimension_y'), '>=', $value);
    }

    public function dimension_z($value)
    {
        return $this->builder->where($this->column('dimension_z'), '>=', $value);
    }

    public function max_weight($value)
    {
        return $this->builder->where($this->column('max_weight'), '>=', $value);
    }

    public function car_type_id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('car_type_id'), $value);
        }
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

    public function active($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('active'), $value);
        }
    }

    public function moderated($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('moderated'), $value);
        }
    }

    public function name($value)
    {
        if ($value != '') {
            return $this->builder->whereHas('user', function ($query) use ($value) {
                $query->whereHas('profile', function ($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%')
                        ->orWhere('surname', 'like', '%' . $value . '%')
                        ->orWhere('middle_name', 'like', '%' . $value . '%');
                });
            });
        }
    }
    
    public function username($value)
    {
        if ($value != '') {
            return $this->builder->whereHas('user', function ($query) use ($value) {
                $query->where('username', 'LIKE', "%$value%");
            });
        }
    }

    public function company_id($value)
    {
        if ($value != '') {
            return $this->builder->whereHas('user', function ($query) use ($value) {
                $query->whereHas('profile', function ($query) use ($value) {
                    $query->where('company_id', $value);
                });
            });
        }
    }

    /**
     * name
     * car_type
     * active
     * sort: created_at / rating / orders_count / orders_commission
     */
    public function sort($value, $concat = null)
    {
        if ($value != '' && ($value == '-rating' || $value == 'rating')) {
            $firstChar = substr($value, 0, 1);
            if ($firstChar === '-') {
                $type = 'desc';
                $value = substr($value, 1);
            } else {
                $type = 'asc';
            }

            $this->builder->leftJoin('user_profiles', function ($join) {
                /**
                 * @var $join \Illuminate\Database\Query\JoinClause
                 */
                $join->on($this->table . '.user_id', 'user_profiles' . '.user_id');
            })
//                ->select([$this->table . '.*', 'user_profiles' . '.rating'])
                ->orderBy('rating', $type);
        }
        if ($value != '' && ($value == '-orders_commission' || $value == 'orders_commission')) {
            $firstChar = substr($value, 0, 1);
            if ($firstChar === '-') {
                $type = 'desc';
                $value = substr($value, 1);
            } else {
                $type = 'asc';
            }

            $this->builder->leftJoin('truck_bookings', function ($join) {
                /**
                 * @var $join \Illuminate\Database\Query\JoinClause
                 */
                $join->on($this->table . '.user_id', 'truck_bookings' . '.driver_id');
                $join->on('status', '=', DB::raw("'done'"));
            })
                ->select([$this->table . '.*', 'truck_bookings.status', DB::raw('SUM(`truck_bookings`.`commission`) as orders_commission')])
                ->groupBy(['cars.id'])
                ->orderBy('orders_commission', $type);
        }
        if ($value != '' && ($value == '-orders_count' || $value == 'orders_count')) {
            $firstChar = substr($value, 0, 1);
            if ($firstChar === '-') {
                $type = 'desc';
                $value = substr($value, 1);
            } else {
                $type = 'asc';
            }

            $this->builder->withCount('truck_bookings')->orderBy('truck_bookings_count', $type);
        }
        return parent::sort($value, $concat);
    }
}
