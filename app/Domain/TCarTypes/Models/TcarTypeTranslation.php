<?php


namespace App\Domain\TCarTypes\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\TCarTypes\Models\TCarTypeTranslation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarTypeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarTypeTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarTypeTranslation query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $tcar_type_id
 * @property string|null $title
 * @property string $locale
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarTypeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarTypeTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarTypeTranslation whereTcarTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\TCarTypes\Models\TcarTypeTranslation whereTitle($value)
 */
class TcarTypeTranslation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
}
