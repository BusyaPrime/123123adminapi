<?php


namespace App\Domain\CarTypes\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\CarTypes\Models\CarTypeTranslation
 *
 * @property int $id
 * @property int $car_type_id
 * @property string|null $title
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarTypeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarTypeTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarTypeTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarTypeTranslation whereCarTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarTypeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarTypeTranslation whereTitle($value)
 * @mixin \Eloquent
 * @property string $locale
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CarTypes\Models\CarTypeTranslation whereLocale($value)
 */
class CarTypeTranslation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
}
