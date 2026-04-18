<?php

namespace App\Domain\Users\Filters;

use App\Services\FilterService\Filter;
use Illuminate\Http\Request;

class UserFilter extends Filter
{
    /**
     * Доступные фильтры.
     *
     * @var array
     */
    protected $available = [
        'id',
        'name',
        'admin_name',
        'username',
        'admin_role_id',
        'company_id',
        'created_at',
        'region_id',
        'role',
        'active',
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
        'username' => '',
        'admin_role_id' => '',
		'company_id',
        'name' => '',
        'admin_name' => '',
        'created_at' => '',
        'region_id' => '',
        'active' => '',
        'company_id' => '',
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

    public function admin_role_id($value)
    {
        return $this->builder->where($this->column('admin_role_id'), $value);
    }

    /**
     * Поиск по имени.
     *
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function name($value)
    {
        if ($value) {
            return $this->builder->whereHas('profile', function ($query) use ($value) {
                $query->where('name', 'like', '%' . $value . '%')
                    ->orWhere('surname', 'like', '%' . $value . '%')
                    ->orWhere('middle_name', 'like', '%' . $value . '%');
            });
        }
    }
    public function admin_name($value)
    {
        if ($value) {
            return $this->builder->where('name', 'like', '%' . $value . '%');
        }
    }

    /**
     * Поиск по имени.
     *
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function role($value)
    {
        if ($value == 'users') {
            return $this->builder->whereDoesntHave('car')->whereDoesntHave('tcar');
        }
        if ($value == 'cars') {
            return $this->builder->whereHas('car');
        }
        if ($value == 'tcars') {
            return $this->builder->whereHas('tcar');
        }

        return $this->builder;
    }

    public function username($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('username'), 'like', '%' . $value . '%');
        }
    }

    public function region_id($value)
    {
        if ($value != '') {
            return $this->builder->whereHas('profile', function ($query) use ($value) {
                $query->where('region_id', $value);
            });
        }
    }

    public function active($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('active'), $value);
        }
    }

    public function created_at($value)
    {
        if ($value != '') {
            return $this->builder->whereBetween($this->column('created_at'), [date('Y-m-d 00:00:00', strtotime($value)), date('Y-m-d 23:59:59', strtotime($value))]);
        }
    }
	
	public function company_id($value)
    {
        if ($value != '') {
            return $this->builder->whereHas('profile', function ($query) use ($value) {
                $query->where('company_id', $value);
            });
        }
    }

}
