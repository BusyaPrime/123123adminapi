<?php


namespace App\Domain\LoadTypes\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\LoadTypes\Models\LoadTypeTranslation
 *
 * @property int $id
 * @property int $load_type_id
 * @property string|null $title
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadTypeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadTypeTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadTypeTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadTypeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadTypeTranslation whereLoadTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadTypeTranslation whereTitle($value)
 * @mixin \Eloquent
 * @property string $locale
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadTypeTranslation whereLocale($value)
 */
class LoadTypeTranslation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
}
