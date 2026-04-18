<?php


namespace App\Domain\TruckBookings\Models;


use App\Domain\CancelReasons\Models\CancelReason;
use App\Domain\CargoTypes\Models\CargoType;
use App\Domain\Cars\Models\Car;
use App\Domain\TruckBookings\Models\TruckBookingView;
use App\Domain\CarTypes\Models\CarType;
use App\Domain\Companies\Models\Company;
use App\Domain\LoadTypes\Models\LoadType;
use App\Domain\Messages\Models\Message;
use App\Domain\Users\Models\UserProfile;
use App\Services\FilterService\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Domain\TruckBookings\Models\BookingPriceOffer;

/**
 * App\Domain\TruckBookings\Models\TruckBooking
 *
 * @property-read \App\Domain\Users\Models\User $driver
 * @property-read \App\Domain\Users\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking query()
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $routes
 * @property int|null $user_id
 * @property int|null $driver_id
 * @property int|null $car_type_id
 * @property int|null $cargo_type_id
 * @property int|null $load_type_id
 * @property int|null $weight
 * @property int|null $dimension_x
 * @property int|null $dimension_y
 * @property int|null $dimension_z
 * @property int|null $need_pack
 * @property int|null $need_provide_loader
 * @property int|null $loader_amount
 * @property string|null $comment
 * @property string|null $distance
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereCarTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereCargoTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereDimensionX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereDimensionY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereDimensionZ($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereLoadTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereLoaderAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereNeedPack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereNeedProvideLoader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereRoutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereWeight($value)
 * @property-read \App\Domain\CarTypes\Models\CarType|null $carType
 * @property-read \App\Domain\CargoTypes\Models\CargoType|null $cargoType
 * @property-read \App\Domain\LoadTypes\Models\LoadType|null $loadType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking withCarType()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking withCargoType()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking withLoadType()
 * @property int|null $price
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking wherePrice($value)
 * @property int|null $commission
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereCommission($value)
 * @property string|null $review
 * @property int|null $rating
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereReview($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Messages\Models\Message[] $messages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\Cars\Models\Car[] $cars
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking filter(\App\Services\FilterService\Filter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking paginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking simplePaginateFilter($perPage = 20)
 * @property string|null $payment_type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking wherePaymentType($value)
 * @property int|null $last_status_update
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereLastStatusUpdate($value)
 * @property int|null $waiting_time
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TruckBookings\Models\TruckBooking whereWaitingTime($value)
 */
class TruckBooking extends Model
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
    const STATUS_CONFIRMATION = 'confirmation';

    const ROUTES_FROM = 'from_routes';
    const ROUTES_TO = 'to_routes';

    const PAY_CASH = 'cash';
    const PAY_TERMINAL = 'terminal';
    const PAY_COMPANY = 'company';
	
	const DRIVER_REMINDER_TIME = '30';

	const CONFIRMATION_LIMIT = 2;
	const CONFIRMATION_LIMIT_RETRY = 1;
    const NEARBY_DRIVERS_RADIUS = 60;
    const BANK_TRANSFER_COEFFICIENT = 0.1;
    const COUNT_ON_PAGE = 20;

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
            static::STATUS_WAITING,
            static::STATUS_ACCEPTED,
            static::STATUS_ARRIVED,
            static::STATUS_CANCELED,
            static::STATUS_PROCESSING,
            static::STATUS_PAUSE,
            static::STATUS_DONE,
            static::STATUS_ORDER,
            static::STATUS_CONFIRMATION,
        ];
    }

    public static function availableStatuses()
    {
        return [
            static::STATUS_ACCEPTED,
            static::STATUS_ARRIVED,
            static::STATUS_PROCESSING,
            static::STATUS_PAUSE,
            static::STATUS_ORDER,
            static::STATUS_CONFIRMATION,
        ];
    }

    public function user()
    {
        return $this->belongsTo(UserProfile::class, 'user_id', 'user_id');
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    
    public function views()
    {
        return $this->hasMany(TruckBookingView::class);
    }


    public function driver()
    {
        return $this->belongsTo(UserProfile::class, 'driver_id', 'user_id');
    }

    public function parseRoutes()
    {
        return json_decode($this->routes, true);
    }

    public function carType()
    {
        return $this->belongsTo(CarType::class);
    }

    public function cargoType()
    {
        return $this->belongsTo(CargoType::class);
    }

    /**
     * The TruckBooking that has many bookingPriceOffers
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function bookingPriceOffer()
    {
        return $this->hasMany(BookingPriceOffer::class, 'booking_id', 'id');
    }

    public function loadType()
    {
        return $this->belongsTo(LoadType::class);
    }

    public function cancelReason()
    {
        return $this->belongsTo(CancelReason::class);
    }

    public function cars()
    {
        return $this->belongsToMany(Car::class);
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
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

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithCargoType($query)
    {
        return $query->with(['cargoType' => function($query) {
            $query->withTranslation();
        }]);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithLoadType($query)
    {
        return $query->with(['loadType' => function($query) {
            $query->withTranslation();
        }]);
    }

    public function findAndAttachCars()
    {
        $builder = Car::where('active', 1)
            ->where('moderated', 1)
            ->where('car_type_id', $this->car_type_id);

        if ($cargoType = $this->cargo_type_id) {
            $builder->whereHas('cargoTypes', function (Builder $query) use ($cargoType) {
                $query->where('cargo_types.id', $cargoType);
            });
        }

        if ($loadType = $this->load_type_id) {
            $builder->whereHas('loadTypes', function (Builder $query) use ($loadType) {
                $query->where('load_types.id', $loadType);
            });
        }
//        if ($this->weight) {
//            $builder->where('max_weight', '>=', $this->weight);
//        }
//        if ($this->dimension_x) {
//            $builder->where('dimension_x', '>=', $this->dimension_x);
//        }
//        if ($this->dimension_y) {
//            $builder->where('dimension_y', '>=', $this->dimension_y);
//        }
//        if ($this->dimension_z) {
//            $builder->where('dimension_z', '>=', $this->dimension_z);
//        }
//        if ($this->need_pack) {
//            $builder->where('can_pack', $this->need_pack);
//        }
//        if ($this->need_provide_loader) {
//            $builder->where('can_provide_loader', $this->need_provide_loader);
//        }
        $cars = $builder->get()->keyBy('id')->toArray();

        $this->cars()->attach(array_keys($cars));
    }

    public function clientCompany(){
        return $this->belongsTo(Company::class, 'client_company_id', 'id');
    }


    public function getDriverCar()
    {
        if (!$this->driver_id) {
            return null;
        }
        $car = Car::where('user_id', $this->driver_id)->first();
        if(!$car) {
            return null;
        }
        return $car;
    }
}
