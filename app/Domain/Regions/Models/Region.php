<?php


namespace App\Domain\Regions\Models;


use App\Domain\CarTypes\Models\CarType;
use Illuminate\Database\Eloquent\Model;
use App\Services\FilterService\Filterable;

class Region extends Model
{
    use Filterable;
    

    public $timestamps = false;
    protected $guarded = ['id'];

    public function carTypes()
    {
        return $this->belongsToMany(CarType::class)->withPivot('price_per_km');
    }

}
