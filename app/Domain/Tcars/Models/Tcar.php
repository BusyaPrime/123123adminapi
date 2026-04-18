<?php


namespace App\Domain\Tcars\Models;


use App\Domain\TCarBookings\Models\TcarBooking;
use App\Domain\TCarTypes\Models\TcarType;
use App\Domain\Tcompanies\Models\Tcompany;
use App\Domain\Users\Models\User;
use App\Services\FilterService\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tcars\Models\Tcar
 *
 * @property int $id
 * @property int $user_id
 * @property int $tcar_type_id
 * @property string|null $model
 * @property string|null $color
 * @property string|null $number
 * @property int|null $peoples
 * @property int|null $ac
 * @property int|null $kids_seat
 * @property int|null $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Domain\TCarTypes\Models\TcarType $carType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Tcompanies\Models\Tcompany[] $companies
 * @property-read \App\Domain\Users\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar filter(\App\Services\FilterService\Filter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar paginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar simplePaginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar whereAc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar whereKidsSeat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar wherePeoples($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar whereTcarTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Tcars\Models\Tcar withCarType()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\TCarBookings\Models\TcarBooking[] $bookings
 */
class Tcar extends Model
{
    use Filterable;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Tcompany::class);
    }

    public function carType()
    {
        return $this->belongsTo(TcarType::class, 'tcar_type_id', 'id');
    }

    public function bookings()
    {
        return $this->belongsToMany(TcarBooking::class);
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

//    /**
//     * @param Builder $query
//     * @return Builder
//     */
//    public function scopeWithAll($query)
//    {
//        return $query->withCargoTypes()
//            ->withCarType()
//            ->withLoadTypes();
//    }
}
