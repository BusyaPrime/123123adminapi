<?php


namespace App\Domain\TCarTypes\Models;


use App\Services\FilterService\Filterable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * App\Domain\TCarTypes\Models\TCarType
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\TCarTypes\Models\TcarTypeTranslation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType filter(\App\Services\FilterService\Filter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType listsTranslations($translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType notTranslatedIn($locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType orWhereTranslation($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType orWhereTranslationLike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType orderByTranslation($key, $sortmethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType paginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType simplePaginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType translated()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType translatedIn($locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType whereTranslation($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType whereTranslationLike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType withTranslation()
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $icon
 * @property int|null $min_distance
 * @property int|null $min_price
 * @property int|null $price_per_km
 * @property float|null $commission
 * @property int|null $priority
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType whereMinDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType whereMinPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType wherePricePerKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType wherePriority($value)
 * @property int|null $price_per_min
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType wherePricePerMin($value)
 * @property int|null $peoples
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarType wherePeoples($value)
 */
class TcarType extends Model
{

    use Translatable, Filterable;

    public $translatedAttributes = ['title'];

    public $timestamps = false;
    protected $guarded = ['id'];

    public $price = null;

    protected static $imagePath = 'uploads/car_types/';

    public static function getImagePath()
    {
        return self::$imagePath;
    }

    public function imageUrl()
    {
        if(!$this->icon) {
            return asset('uploads/defaults/car.png');
        }
        return asset(self::getImagePath().$this->icon);
    }

    public function uploadImage(UploadedFile $image)
    {
        $extension = $image->getClientOriginalExtension();
        $filename = $this->id.'_'.uniqid().'.'.$extension;
        \Image::make($image)->save(public_path(self::getImagePath().$filename));
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

    public function setPrice($distance = 0)
    {
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
}
