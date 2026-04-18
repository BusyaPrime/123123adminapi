<?php
/**
 * Created by PhpStorm.
 * User: irock
 * Date: 28.02.2019
 * Time: 12:48
 */

namespace App\Domain\Contacts\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Contacts\Models\Contact
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Contacts\Models\Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Contacts\Models\Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Contacts\Models\Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Contacts\Models\Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Contacts\Models\Contact whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Contacts\Models\Contact whereValue($value)
 * @mixin \Eloquent
 */
class Contact extends Model
{
    protected $guarded = ['id'];

    public static $keysAvailable = [
        'phone',
    ];

    public $timestamps = false;
}
