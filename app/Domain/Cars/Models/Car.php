<?php


namespace App\Domain\Cars\Models;


use App\Domain\CargoTypes\Models\CargoType;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\Companies\Models\Company;
use App\Domain\LoadTypes\Models\LoadType;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Models\User;
use App\Domain\Users\Models\UserProfile;
use App\Services\FilterService\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Cars\Models\Car
 *
 * @property int $id
 * @property int $user_id
 * @property int $car_type_id
 * @property string|null $model
 * @property string|null $color
 * @property string|null $number
 * @property int|null $max_weight
 * @property int|null $dimension_x
 * @property int|null $dimension_y
 * @property int|null $dimension_z
 * @property int|null $can_pack
 * @property int|null $can_provide_loader
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Domain\CarTypes\Models\CarType $carType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\CargoTypes\Models\CargoType[] $cargoTypes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\LoadTypes\Models\LoadType[] $loadTypes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereCanPack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereCanProvideLoader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereCarTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereDimensionX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereDimensionY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereDimensionZ($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereMaxWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car withAll()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car withCarType()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car withCargoTypes()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car withLoadTypes()
 * @mixin \Eloquent
 * @property-read \App\Domain\Users\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car filter(\App\Services\FilterService\Filter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car paginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car simplePaginateFilter($perPage = 20)
 * @property int|null $active
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Cars\Models\Car whereActive($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Companies\Models\Company[] $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\TruckBookings\Models\TruckBooking[] $bookings
 */
class Car extends Model
{
    use Filterable;

    protected $guarded = ['id'];
    protected $fillable = ['user_id'];
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function truck_bookings()
    {
        return $this->hasMany(TruckBooking::class, 'driver_id', 'user_id');
    }

    public function cargoTypes()
    {
        return $this->belongsToMany(CargoType::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function loadTypes()
    {
        return $this->belongsToMany(LoadType::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(TruckBooking::class);
    }

    public function carType()
    {
        return $this->belongsTo(CarType::class);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithCargoTypes($query)
    {
        return $query->with(['cargoTypes' => function($query) {
            $query->withTranslation();
        }]);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithLoadTypes($query)
    {
        return $query->with(['loadTypes' => function($query) {
            $query->withTranslation();
        }]);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithCarType($query)
    {
        return $query->with(['carType' => function($query) {
            $query->withTranslation();
        }]);
    }
}
