<?php


namespace App\Domain\CargoTypes\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\CargoTypes\Models\CargoTypeTranslation
 *
 * @property int $id
 * @property int $cargo_type_id
 * @property string|null $title
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoTypeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoTypeTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoTypeTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoTypeTranslation whereCargoTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoTypeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoTypeTranslation whereTitle($value)
 * @mixin \Eloquent
 * @property string $locale
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\CargoTypes\Models\CargoTypeTranslation whereLocale($value)
 */
class CargoTypeTranslation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
}
