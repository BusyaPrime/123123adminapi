<?php


namespace App\Domain\Users\Models;


use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Models\User;

/**
 * App\Domain\Users\Models\UserCode
 *
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Domain\Users\Models\UserCode whereUserId($value)
 * @mixin \Eloquent
 */
class UserCode extends Model
{
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
