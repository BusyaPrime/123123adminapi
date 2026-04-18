<?php


namespace App\Domain\CarTypes\Models;


use App\Domain\Regions\Models\Region;
use App\Domain\CarTypes\Models\CarTypeRate;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Services\FilterService\Filterable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * App\Domain\CarTypes\Models\CarType
 *
 * @property int $id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\CarTypes\Models\CarTypeTranslation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType listsTranslations($translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType notTranslatedIn($locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType orWhereTranslation($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType orWhereTranslationLike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType orderByTranslation($key, $sortmethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType translated()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType translatedIn($locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType whereTranslation($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType whereTranslationLike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType withTranslation()
 * @mixin \Eloquent
 * @property int|null $priority
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType wherePriority($value)
 * @property string|null $icon
 * @property int|null $min_distance
 * @property int|null $min_price
 * @property int|null $price_per_km
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType filter(\App\Services\FilterService\Filter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType paginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType simplePaginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType whereMinDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType whereMinPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType wherePricePerKm($value)
 * @property float|null $commission
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType whereCommission($value)
 * @property int|null $price_per_min
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType wherePricePerMin($value)
 * @property int|null $max_weight
 * @property int|null $dimension_x
 * @property int|null $dimension_y
 * @property int|null $dimension_z
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType whereDimensionX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType whereDimensionY($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType whereDimensionZ($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarType whereMaxWeight($value)
 */
class CarType extends Model
{
    use Translatable, Filterable;

    public $translatedAttributes = ['title'];

    public $timestamps = false;

    public $price = null;
    public $calculatedPrice = 0;
    public $calculatedPriceNotFull = 0;
    public $notFullEnabled = false;

    protected $guarded = ['id'];

    protected static $imagePath = 'uploads/car_types/';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defaultLocale = app()->getLocale();
    }

    public static function getImagePath()
    {
        return self::$imagePath;
    }

    public function imageUrl()
    {
        if (!$this->icon) {
            return asset('uploads/defaults/truck.png');
        }
        return asset(self::getImagePath().$this->icon);
    }

    public function bigImageUrl()
    {
        if (!$this->big_icon) {
            return asset('uploads/defaults/truck.png');
        }
        return asset(self::getImagePath().$this->big_icon);
    }

    public function uploadImage(UploadedFile $image)
    {
        $extension = $image->getClientOriginalExtension();
        $filename = $this->id.'_'.uniqid().'.'.$extension;
        if($extension != 'svg'){
            \Image::make($image)->save(public_path(self::getImagePath().$filename));
        } else{
            \Storage::disk('uploads')->putFileAs(self::getImagePath(), $image, $filename).'<br />';
        }
        return $filename;
    }


    public function deleteImage()
    {
        $imagePath = public_path(self::getImagePath().$this->icon);
        if ($this->icon != '' && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $this->icon = null;
    }

    public function regions()
    {
        return $this->belongsToMany(Region::class)->withPivot('price_per_km');
    }
    
    public function car_type_rates()
    {
        return $this->hasMany(CarTypeRate::class);
    }

    public function setPrice($distance = 0, $regionId = null)
    {
        if ($regionId) {
            $region = $this->regions()->find($regionId);
            $this->price_per_km = $region ? $region->pivot->price_per_km : $this->price_per_km;
        }
        if ($distance <= $this->min_distance) {
            $this->price = $this->min_price;
        } else {
            $this->price = ceil($this->min_price + (($distance - $this->min_distance) * $this->price_per_km));
        }
    }

    public function getPrice()
    {
        return $this->price ?? $this->min_price;
    }

    public function truck_bookings()
    {
        return $this->hasMany(TruckBooking::class);
    }
}
