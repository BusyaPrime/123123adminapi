<?php


namespace App\Domain\TCarBookings\Models;


use App\Domain\Messages\Models\TcarMessage;
use App\Domain\Tcars\Models\Tcar;
use App\Domain\TCarTypes\Models\TcarType;
use App\Domain\Users\Models\UserProfile;
use App\Services\FilterService\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\TCarBookings\Models\TcarBooking
 *
 * @property int $id
 * @property string|null $routes
 * @property int|null $user_id
 * @property int|null $driver_id
 * @property int|null $tcar_type_id
 * @property int|null $price
 * @property int|null $peoples
 * @property int|null $ac
 * @property int|null $kids_seat
 * @property string|null $comment
 * @property string|null $distance
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Domain\TCarTypes\Models\TcarType $carType
 * @property-read \App\Domain\Users\Models\UserProfile|null $driver
 * @property-read \App\Domain\Users\Models\UserProfile|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereAc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereKidsSeat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking wherePeoples($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereRoutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereTcarTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking withCarType()
 * @mixin \Eloquent
 * @property string|null $date
 * @property string|null $time
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereTime($value)
 * @property int|null $commission
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereCommission($value)
 * @property string|null $review
 * @property int|null $rating
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereReview($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Messages\Models\TcarMessage[] $messages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Tcars\Models\Tcar[] $cars
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking filter(\App\Services\FilterService\Filter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking paginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking simplePaginateFilter($perPage = 20)
 * @property string|null $payment_type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking wherePaymentType($value)
 * @property int|null $last_status_update
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereLastStatusUpdate($value)
 * @property int|null $waiting_time
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarBookings\Models\TcarBooking whereWaitingTime($value)
 */
class TcarBooking extends Model
{
    use Filterable;

    protected $guarded = ['id'];

    const STATUS_NEW = 'new';
    const STATUS_WAITING = 'waiting';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_ARRIVED = 'arrived';
    const STATUS_CANCELED = 'canceled';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PAUSE = 'pause';
    const STATUS_DONE = 'done';
    const STATUS_ORDER = 'order';

    const PAY_CASH = 'cash';
    const PAY_TERMINAL = 'terminal';
    const PAY_COMPANY = 'company';

    public static function paymentTypes()
    {
        return [
            static::PAY_CASH,
            static::PAY_TERMINAL,
            static::PAY_COMPANY,
        ];
    }

    public static function statuses()
    {
        return [
            static::STATUS_NEW,
            static::STATUS_WAITING,
            static::STATUS_ACCEPTED,
            static::STATUS_ARRIVED,
            static::STATUS_CANCELED,
            static::STATUS_PROCESSING,
            static::STATUS_PAUSE,
            static::STATUS_DONE,
            static::STATUS_ORDER,
        ];
    }

    public function cars()
    {
        return $this->belongsToMany(Tcar::class);
    }

    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_id', 'user_id');
    }

    public function driver()
    {
        return $this->belongsTo(UserProfile::class, 'driver_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(TcarMessage::class);
    }

    public function parseRoutes()
    {
        return json_decode($this->routes, true);
    }

    public function carType()
    {
        return $this->belongsTo(TcarType::class, 'tcar_type_id', 'id');
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

    public function findAndAttachCars()
    {
        $builder = Tcar::where('active', 1)
            ->where('tcar_type_id', $this->tcar_type_id);

        if ($this->peoples) {
            $builder->where('peoples', '>=', $this->peoples);
        }

        if ($this->ac) {
            $builder->where('ac', $this->ac);
        }
        if ($this->kids_seat) {
            $builder->where('kids_seat', $this->kids_seat);
        }
        $cars = $builder->get()->keyBy('id')->toArray();

        $this->cars()->attach(array_keys($cars));
    }

    public function getDriverCar()
    {
        if (!$this->driver_id) {
            return null;
        }
        $car = Tcar::where('user_id', $this->driver_id)->first();
        if(!$car) {
            return null;
        }
        return $car;
    }
}
