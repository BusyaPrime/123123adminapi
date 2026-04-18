<?php


namespace App\Domain\CargoTypes\Models;


use App\Domain\TruckBookings\Models\TruckBooking;
use App\Services\FilterService\Filterable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\CargoTypes\Models\CargoType
 *
 * @property int $id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\CargoTypes\Models\CargoTypeTranslation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType listsTranslations($translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType notTranslatedIn($locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType orWhereTranslation($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType orWhereTranslationLike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType orderByTranslation($key, $sortmethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType translated()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType translatedIn($locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType whereTranslation($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType whereTranslationLike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType withTranslation()
 * @mixin \Eloquent
 * @property int|null $priority
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType filter(\App\Services\FilterService\Filter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType paginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoType simplePaginateFilter($perPage = 20)
 */
class CargoType extends Model
{
    use Translatable, Filterable;

    public $translatedAttributes = ['title'];

    public $timestamps = false;
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defaultLocale = app()->getLocale();
    }

    public function truck_bookings()
    {
        return $this->hasMany(TruckBooking::class);
    }
}
