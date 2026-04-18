<?php

namespace App\Domain\CarTypes\Models;

use Illuminate\Database\Eloquent\Model;

use App\Domain\CarTypes\Models\CarTypeRate;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\Regions\Models\Region;
use App\Domain\Seasons\Models\Season;

class CarTypeRatesHistory extends Model
{
    protected $guarded = ['id'];
    
    
    public function car_type_rate()
    {
        return $this->belongsTo(CarTypeRate::class);
    }
    
    public function car_type()
    {
        return $this->belongsTo(CarType::class);
    }
    
    public function region_from()
    {
        return $this->belongsTo(Region::class, 'region_from_id');
    }
    
    public function region_to()
    {
        return $this->belongsTo(Region::class, 'region_to_id');
    }
    
    public function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }
}
