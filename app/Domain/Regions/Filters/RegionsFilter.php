<?php


namespace App\Domain\Regions\Filters;

use App\Services\FilterService\Filter;
use Illuminate\Http\Request;

class RegionsFilter extends Filter
{

    protected $available = [
        'id', 'title', 'region_from', 'region_to'
    ];

    protected $defaults = [
        'sort' => '-id',
        'id' => '',
        'title' => '',
        'region_from' => '',
        'region_to' => '',
    ];

    public function __construct(Request $request)
    {
        $this->input = $this->prepareInput($request->all());
    }

    public function id($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('id'), $value);
        }
    }
    
    public function region_from($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('id'), $value);
        }
    }
    
    public function region_to($value)
    {
        if ($value != '') {
            return $this->builder->where($this->column('id'), $value);
        }
    }
}
