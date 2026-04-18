<?php


namespace App\Domain\LoadTypes\Models;


use App\Services\FilterService\Filterable;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\LoadTypes\Models\LoadType
 *
 * @property int $id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain\LoadTypes\Models\LoadTypeTranslation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType listsTranslations($translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType notTranslatedIn($locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType orWhereTranslation($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType orWhereTranslationLike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType orderByTranslation($key, $sortmethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType translated()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType translatedIn($locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType whereTranslation($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType whereTranslationLike($key, $value, $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType withTranslation()
 * @mixin \Eloquent
 * @property int|null $priority
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType filter(\App\Services\FilterService\Filter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType paginateFilter($perPage = 20)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\LoadTypes\Models\LoadType simplePaginateFilter($perPage = 20)
 */
class LoadType extends Model
{
    use Translatable, Filterable;

    public $translatedAttributes = ['title'];

    public $timestamps = false;
    protected $guarded = ['id'];
}
